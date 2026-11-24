<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Person;
use App\Models\Reservation;
use App\Models\ReservationCredit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Gestion des crédits compensatoires (fermetures de piscine).
 *
 * Un crédit « disponible » = status pending ET used_in_reservation_id NULL.
 * Un crédit « réservé »    = status pending ET used_in_reservation_id = réservation en attente.
 * Un crédit « utilisé »    = status used (après paiement de la réservation).
 * Un même crédit ne peut donc jamais être déduit de deux réservations.
 */
class ReservationCreditService
{
    /** Montant total des crédits disponibles (non réservés) d'un utilisateur. */
    public function availableAmount(int $userId): float
    {
        return (float) $this->availableQuery($userId)->sum('credited_amount');
    }

    /**
     * Synthèse complète du solde d'un client (source unique du bandeau « mes réservations ») :
     * total accordé (hors annulés), utilisé (payé), réservé (en attente), disponible, + une ligne par crédit.
     * `available` est exactement availableAmount().
     */
    public function summary(int $userId): array
    {
        $credits = ReservationCredit::where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->orderBy('closure_date')->orderBy('id')
            ->get();

        $state = function ($c) {
            if ($c->status === 'used') {
                return 'used';
            }

            return $c->used_in_reservation_id ? 'reserved' : 'available';
        };

        $sum = fn (string $s) => round((float) $credits->filter(fn ($c) => $state($c) === $s)->sum('credited_amount'), 2);

        $reservations = Reservation::with('season')
            ->whereIn('id', $credits->pluck('used_in_reservation_id')->filter()->unique())
            ->where('user_id', $userId)
            ->get()->keyBy('id');

        return [
            'total'     => round((float) $credits->sum('credited_amount'), 2),
            'used'      => $sum('used'),
            'reserved'  => $sum('reserved'),
            'available' => $sum('available'),
            'rows'      => $credits->map(fn ($c) => [
                'closure_date' => $c->closure_date,
                'amount'       => (float) $c->credited_amount,
                'state'        => $state($c),
                'reservation'  => $c->used_in_reservation_id ? $reservations->get($c->used_in_reservation_id) : null,
            ])->all(),
        ];
    }

    /**
     * Réserve pour $reservation des crédits disponibles, dans la limite de $price,
     * puis met à jour total_price. Retourne le montant déduit.
     * Seuls des crédits entiers sont pris : jamais plus que le prix de la réservation.
     */
    public function reserveFor(Reservation $reservation, float $price): float
    {
        return DB::transaction(function () use ($reservation, $price) {
            $credits = $this->availableQuery((int) $reservation->user_id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $deducted = 0.0;
            $ids = [];

            foreach ($credits as $credit) {
                $amount = (float) $credit->credited_amount;
                if ($amount <= 0 || round($deducted + $amount, 2) > round($price, 2)) {
                    continue;
                }
                $deducted += $amount;
                $ids[] = $credit->id;
            }

            if ($ids) {
                ReservationCredit::whereIn('id', $ids)->update([
                    'used_in_reservation_id' => $reservation->id,
                    'updated_at'             => now(),
                ]);
            }

            $reservation->total_price     = max(0, round($price - $deducted, 2));
            $reservation->credit_discount = round($deducted, 2);

            // Soldes figés à la création : avant = crédits disponibles (dont ceux pris ici), après = encore libres
            $before = round((float) $credits->sum('credited_amount'), 2);
            $detail = (array) ($reservation->price_detail ?? []);
            $detail['credit_balance_before'] = $before;
            $detail['credit_balance_after']  = round($before - $deducted, 2);
            $reservation->price_detail = $detail;

            $reservation->save();

            return round($deducted, 2);
        });
    }

    /**
     * Action « payer » : si des crédits sont réservés pour cette réservation et que le reste
     * arrondi est 0 DA, la réservation est réglée sans passerelle. Idempotent (verrou + test du statut).
     * Retourne true si la réservation est (désormais) payée par remboursement, false sinon.
     */
    public function payWithCredits(Reservation $reservation): bool
    {
        return DB::transaction(function () use ($reservation) {
            $locked = Reservation::whereKey($reservation->id)->lockForUpdate()->first();

            if (!$locked || $locked->payment_status === 'paid') {
                return false;
            }

            $hasCredits = ReservationCredit::where('used_in_reservation_id', $locked->id)
                ->where('status', 'pending')->exists();

            if (!$hasCredits || !self::belowThreshold((float) $locked->total_price)) {
                return false;
            }

            $this->settleFreeReservation($locked);

            return true;
        });
    }

    /** Seuil unique (config/reservation.php) : reste à payer < seuil => règlement par remboursement. */
    public static function threshold(): float
    {
        return (float) config('reservation.credit_waive_threshold', 100);
    }

    public static function belowThreshold(float $remaining): bool
    {
        return round($remaining, 2) < self::threshold();
    }

    /** Montant réellement envoyé à la passerelle (même arrondi que PaymentController::initiate). */
    public function payableAmount(Reservation $reservation): int
    {
        return (int) round($reservation->total_price);
    }

    /** Libère les crédits réservés (réservation supprimée / abandonnée avant paiement). */
    public function release(Reservation $reservation): void
    {
        // Réservation entièrement payée par remboursement (paiement FREE-…) : aucun argent n'a été
        // versé, le client récupère donc ses crédits déjà « used ».
        if ($reservation->payment_id
            && Payment::where('id', $reservation->payment_id)->where('order_id', 'like', 'FREE-%')->exists()) {
            ReservationCredit::where('used_in_reservation_id', $reservation->id)
                ->where('status', 'used')
                ->update(['status' => 'pending', 'used_in_reservation_id' => null, 'updated_at' => now()]);
        }

        ReservationCredit::where('used_in_reservation_id', $reservation->id)
            ->where('status', 'pending')
            ->update(['used_in_reservation_id' => null, 'updated_at' => now()]);
    }

    /** Après paiement : les crédits réservés pour cette réservation passent à « used ». */
    public function consume(Reservation $reservation): void
    {
        ReservationCredit::where('used_in_reservation_id', $reservation->id)
            ->where('status', 'pending')
            ->update(['status' => 'used', 'updated_at' => now()]);
    }

    /**
     * Réservation dont le montant à payer est 0 DA : pas de passerelle,
     * la réservation est directement payée / confirmée.
     */
    public function settleFreeReservation(Reservation $reservation): void
    {
        DB::transaction(function () use ($reservation) {
            $payment = Payment::create([
                'order_id'      => 'FREE-' . Str::uuid(),
                'amount'        => 0,
                'status'        => Payment::STATUS_SUCCESS,
                'payload'       => ['note' => 'Réservation couverte par le solde compensatoire (0 DA)'],
                'datetimesatim' => now(),
            ]);

            $waived = max(0, round((float) $reservation->total_price, 2)); // petit reste abandonné
            $reservation->credit_waived = $waived;

            $reservation->update([
                'payment_id'     => $payment->id,
                'payment_status' => 'paid',
                'statut'         => 'confirmee',
                'total_price'    => 0,
                'credit_waived'  => $waived,
                'credit_note'    => $this->buildNote($reservation),
            ]);

            // Mêmes effets qu'un paiement réussi (PaymentController::verify)
            if (!empty($reservation->user_id)) {
                $person = !empty($reservation->person_id) ? Person::find($reservation->person_id) : null;
                $person ??= Person::where('user_id', $reservation->user_id)->whereNull('parent_id')->first();

                $person?->update([
                    'etat_ass'           => 0,
                    'updated_at'         => now(),
                    'assured_expires_on' => now(),
                ]);
            }

            $this->consume($reservation);
        });
    }

    /** Remarque : « مدفوع بالتعويض : 666.66 دج (الأرصدة رقم 63, 64, 737) » */
    private function buildNote(Reservation $reservation): string
    {
        $ids = ReservationCredit::where('used_in_reservation_id', $reservation->id)
            ->orderBy('id')->pluck('id')->implode(', ');

        $note = sprintf('مدفوع بالتعويض : %s دج (الأرصدة رقم %s)',
            number_format((float) $reservation->credit_discount, 2, '.', ''), $ids);

        if ((float) $reservation->credit_waived > 0) {
            $note .= sprintf(' (باقي %s دج مُسقَط)', number_format((float) $reservation->credit_waived, 2, '.', ''));
        }

        return $note;
    }

    private function availableQuery(int $userId)
    {
        return ReservationCredit::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereNull('used_in_reservation_id');
    }
}

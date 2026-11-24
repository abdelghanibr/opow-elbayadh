<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\ReservationCredit;
use App\Models\User;
use App\Services\ReservationCreditService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Cas réel : crédits 3 x 222.22 = 666.66, réservation novembre (prorata 666.67) puis décembre (2000).
 * ATTENTION : à lancer sur une base locale (DatabaseTransactions annule tout à la fin).
 */
class ReservationCreditDeductionTest extends TestCase
{
    use DatabaseTransactions;

    private function userWithCredits(array $amounts = [222.22, 222.22, 222.22]): User
    {
        $user = User::create([
            'name' => 'Test credit', 'email' => 'credit-test-' . uniqid() . '@example.test',
            'password' => bcrypt('x'), 'type' => 'person',
        ]);

        // reservation d'origine (payée) pour satisfaire la clé logique
        $origin = Reservation::create(['user_id' => $user->id, 'total_price' => 2000,
            'statut' => 'confirmee', 'payment_status' => 'paid']);

        foreach ($amounts as $i => $a) {
            ReservationCredit::create([
                'reservation_id' => $origin->id, 'user_id' => $user->id,
                'closure_date' => '2026-08-' . (10 + $i), 'credited_amount' => $a, 'status' => 'pending',
            ]);
        }

        return $user;
    }

    private function newReservation(User $user, float $price): Reservation
    {
        return Reservation::create(['user_id' => $user->id, 'total_price' => $price,
            'statut' => 'en_attente', 'payment_status' => 'pending']);
    }

    public function test_credit_is_deducted_only_once_across_two_pending_reservations(): void
    {
        $svc  = app(ReservationCreditService::class);
        $user = $this->userWithCredits();

        $nov = $this->newReservation($user, 666.67);
        $dec = $this->newReservation($user, 2000);

        $svc->reserveFor($nov, 666.67);
        $svc->reserveFor($dec, 2000);

        $this->assertEquals(0.01, (float) $nov->fresh()->total_price);
        $this->assertEquals(2000.00, (float) $dec->fresh()->total_price);
        $this->assertSame(0.0, $svc->availableAmount($user->id));
    }

    public function test_zero_amount_reservation_is_settled_as_paid_and_credits_used(): void
    {
        $svc  = app(ReservationCreditService::class);
        $user = $this->userWithCredits();
        $nov  = $this->newReservation($user, 666.67);

        $svc->reserveFor($nov, 666.67);
        $this->assertTrue(ReservationCreditService::belowThreshold((float) $nov->fresh()->total_price));

        $svc->settleFreeReservation($nov->fresh());

        $nov = $nov->fresh();
        $this->assertSame('paid', $nov->payment_status);
        $this->assertSame('confirmee', $nov->statut);
        $this->assertSame(3, ReservationCredit::where('used_in_reservation_id', $nov->id)->where('status', 'used')->count());
        $this->assertSame('success', DB::table('payments')->where('id', $nov->payment_id)->value('status'));
    }

    public function test_deduction_is_capped_at_reservation_price(): void
    {
        $svc  = app(ReservationCreditService::class);
        $user = $this->userWithCredits([300, 300, 300]);
        $res  = $this->newReservation($user, 500);

        $deducted = $svc->reserveFor($res, 500);

        $this->assertEquals(300.0, $deducted);                 // un seul crédit entier tient dans 500
        $this->assertEquals(200.0, (float) $res->fresh()->total_price);
        $this->assertEquals(600.0, $svc->availableAmount($user->id)); // le reste reste disponible
    }

    public function test_zero_price_without_credits_is_never_auto_paid(): void
    {
        $svc  = app(ReservationCreditService::class);
        $user = $this->userWithCredits([]);
        $res  = $this->newReservation($user, 0);

        $svc->reserveFor($res, 0);
        $this->assertFalse($svc->payWithCredits($res));
        $this->assertSame('pending', $res->fresh()->payment_status);
    }

    public function test_full_cover_writes_the_remark_and_partial_cover_only_the_discount(): void
    {
        $svc  = app(ReservationCreditService::class);
        $user = $this->userWithCredits();
        $nov  = $this->newReservation($user, 666.67);

        $svc->reserveFor($nov, 666.67);
        $this->assertSame('pending', $nov->fresh()->payment_status);
        $this->assertTrue($svc->payWithCredits($nov));
        $this->assertFalse($svc->payWithCredits($nov)); // idempotent
        $nov = $nov->fresh();
        $ids = ReservationCredit::where('used_in_reservation_id', $nov->id)->orderBy('id')->pluck('id')->implode(', ');
        $this->assertSame('مدفوع بالتعويض : 666.66 دج (الأرصدة رقم ' . $ids . ') (باقي 0.01 دج مُسقَط)', $nov->credit_note);
        $this->assertEquals(0.01, (float) $nov->credit_waived);
        $this->assertEquals(0.0, (float) $nov->total_price);
        $this->assertEquals(666.66, (float) $nov->credit_discount);

        $user2 = $this->userWithCredits([200]);
        $part  = $this->newReservation($user2, 500);
        $svc->reserveFor($part, 500);
        $this->assertFalse($svc->payWithCredits($part));
        $part = $part->fresh();
        $this->assertSame('pending', $part->payment_status);
        $this->assertNull($part->credit_note);
        $this->assertEquals(200.0, (float) $part->credit_discount);
        $this->assertEquals(300.0, (float) $part->total_price);
    }

    public function test_credits_are_released_when_pending_reservation_is_deleted(): void
    {
        $svc  = app(ReservationCreditService::class);
        $user = $this->userWithCredits();
        $res  = $this->newReservation($user, 2000);

        $svc->reserveFor($res, 2000);
        $this->assertSame(0.0, $svc->availableAmount($user->id));

        $res->delete();

        $this->assertEquals(666.66, $svc->availableAmount($user->id));
    }

    public function test_payment_success_consumes_only_the_credits_of_that_reservation(): void
    {
        $svc  = app(ReservationCreditService::class);
        $user = $this->userWithCredits([222.22, 222.22]);
        $a    = $this->newReservation($user, 250);
        $b    = $this->newReservation($user, 250);

        $svc->reserveFor($a, 250);   // prend 1 crédit
        $svc->reserveFor($b, 250);   // prend l'autre
        $svc->consume($a);

        $this->assertSame(1, ReservationCredit::where('status', 'used')->where('used_in_reservation_id', $a->id)->count());
        $this->assertSame(1, ReservationCredit::where('status', 'pending')->where('used_in_reservation_id', $b->id)->count());
    }
}

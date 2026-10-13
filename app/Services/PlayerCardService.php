<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Person;
use App\Models\Season;
use App\Models\Reservation;
use App\Models\PersonAssurance;
use App\Models\Complex;

class PlayerCardService
{
    public const STATUS_VALID = 'سارية';
    public const STATUS_EXPIRED = 'منتهية';
    public const STATUS_STOPPED = 'موقوفة';

    /**
     * المدة الرياضية الحالية حسب التاريخ الفعلي.
     * نفضل الموسم الكامل (type_season = season) الذي يحتوي اليوم.
     */
    public function currentSeason(): ?Season
    {
        $now = now();

        // 1) موسم كامل نشط
        $season = Season::where('type_season', 'season')
            ->whereDate('date_debut', '<=', $now)
            ->whereDate('date_fin', '>=', $now)
            ->orderByDesc('date_fin')
            ->first();

        // 2) أي سطر موسم يحتوي اليوم
        if (! $season) {
            $season = Season::whereDate('date_debut', '<=', $now)
                ->whereDate('date_fin', '>=', $now)
                ->orderByDesc('date_fin')
                ->first();
        }

        return $season;
    }

    /**
     * تسمية الموسم مثل: 2026/2027 (مبنية على التاريخ الحالي إن لم يوجد سطر موسم).
     */
    public function seasonLabel(?Season $season = null): string
    {
        $now = now();

        if ($season && preg_match('/\d{4}\s*\/\s*\d{4}/', $season->name)) {
            return trim($season->name);
        }

        $startYear = $now->month >= 7 ? $now->year : $now->year - 1;

        return $startYear . '/' . ($startYear + 1);
    }

    /**
     * رمز عام فريد للبطاقة (لا يحتوي معرف اللاعب في العنوان).
     */
    public function token(int $personId, int $seasonId): string
    {
        $payload = $personId . '|' . $seasonId;
        $sig = substr(hash_hmac('sha256', 'card:' . $payload, (string) config('app.key')), 0, 32);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=') . '.' . $sig;
    }

    /**
     * فكّ رمز البطاقة والتحقق من توقيعه.
     */
    public function resolveToken(string $token): ?array
    {
        if (! preg_match('/^([A-Za-z0-9_-]+)\.([a-f0-9]{32})$/', $token, $m)) {
            return null;
        }

        $payload = base64_decode(strtr($m[1], '-_', '+/'));

        if ($payload === false || ! preg_match('/^(\d+)\|(\d+)$/', $payload, $pm)) {
            return null;
        }

        $personId = (int) $pm[1];
        $seasonId = (int) $pm[2];

        $expected = substr(hash_hmac('sha256', 'card:' . $payload, (string) config('app.key')), 0, 32);

        if (! hash_equals($expected, $m[2])) {
            return null;
        }

// تحقق أن المواسم/اللاعب موجودين فعلاً (منع الغش بالمعرفات المحذوفة)
        if (! Person::whereKey($personId)->exists()) {
            return null;
        }

        if ($seasonId !== 0 && ! Season::whereKey($seasonId)->exists()) {
            return null;
        }

        return ['personId' => $personId, 'seasonId' => $seasonId];
    }

    /**
     * بيانات البطاقة كاملة (للخصوصية: البيانات العامة فقط تُستخدم في صفحة التحقق).
     */
    public function build(Person $person, ?Season $season = null): array
    {
        $season = $season ?: $this->currentSeason();
        $label  = $this->seasonLabel($season);

        $dossier = $person->dossier()->where('owner_type', 'person')->first();

        // 🔎 الحجز المتفعّل لهذا الموسم (مدفوع ومقبول إن وجد)
        $activeReservation = $this->activeReservation($person, $season);
        $assuranceValid    = $this->assuranceIsValid($person);
        $photo             = $person->photo;
        $club              = optional($person->club)->nom;
        $complex           = $person->complex
                            ? $person->complex->nom
                            : optional($activeReservation?->complexActivity?->complex)->nom;
        // النشاط المختار: نشاط النادي > آخر حجز للحساب > النشاط المفضل
        $activity          = $person->club?->activity?->title
                            ?? $person->lastReservation?->complexActivity?->activity?->title
                            ?? $person->favorite_activity;

        [$days, $hours] = $this->trainingSchedule($activeReservation);

        // 🚫 شروط الإصدار
        $blockers = [];
        $warnings = [];

        if (! $dossier || $dossier->etat !== 'approved') {
            $blockers[] = 'ملف اللاعب غير مكتمل أو غير معتمد بعد.';
        }

        if (! $season) {
            $blockers[] = 'لا توجد موسم رياضي ساري المفعول حالياً.';
        }

        if ($season && ! $activeReservation) {
            $blockers[] = 'لا يوجد تسجيل مؤكد ومدفوع لهذا الموسم الرياضي.';
        }

        if (! $photo) {
            $warnings[] = 'أضف صورة شخصية لإصدار البطاقة.';
        }

        if ($season && ! $assuranceValid) {
            $warnings[] = 'التأمين الصحي غير سارٍ لهذا الموسم.';
        }

        // 🏷️ الحالة
        if ($dossier && $dossier->etat === 'rejected') {
            $status = self::STATUS_STOPPED;
        } elseif ($blockers) {
            $status = self::STATUS_EXPIRED;
        } else {
            $status = self::STATUS_VALID;
        }

        $issuedAt  = $season?->date_debut ? Carbon::parse($season->date_debut)->format('d/m/Y') : now()->format('d/m/Y');
        $expiresAt = $season?->date_fin ? Carbon::parse($season->date_fin)->format('d/m/Y') : now()->addYear()->format('d/m/Y');

        $verifyUrl = route('card.verify', $this->token($person->id, $season ? $season->id : 0));

        return [
            'person'          => $person,
            'season'          => $season,
            'seasonLabel'     => $label,
            'title'           => 'بطاقة لاعب للموسم الرياضي ' . $label,
            'cardNumber'      => $this->cardNumber($person->id, $label),
            'issuedAt'        => $issuedAt,
            'expiresAt'       => $expiresAt,
            'status'          => $status,
            'statusReason'    => implode(' ', $blockers ?: $warnings),
            'blockers'        => $blockers,
            'warnings'        => $warnings,
            'canIssue'        => $status === self::STATUS_VALID,
            'firstname'       => $person->firstname,
            'lastname'        => $person->lastname,
            'bloodType'       => $person->blood_type,
            'category'        => optional($person->ageCategory)->name,
            'licenseNumber'   => $person->license_number,
            'club'            => $club,
            'complex'         => $complex ?: optional($person->complex)->nom,
            'activity'        => $activity,
            'address'         => $person->address,
            'days'            => $days,
            'hours'           => $hours,
            'photo'           => $photo ? asset($photo) : asset('images/default-user.png'),
            'photoAvailable'  => (bool) $photo,
            'verifyUrl'       => $verifyUrl,
            'logo'            => is_file(public_path('images/djs-logo.png')) ? asset('images/djs-logo.png') : null,
        ];
    }

    /**
     * الحجز النشط للاعب في الموسم (مدفوع، ثم مقبول).
     */
    protected function activeReservation(Person $person, ?Season $season): ?Reservation
    {
        $query = Reservation::where('person_id', $person->id);

        if ($season) {
            $query->where('season_id', $season->id);
        }

        return $query->orderByRaw("CASE WHEN payment_status = 'paid' THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->first();
    }

    /**
     * أيام و أوقات التدريب من الحجز (أو الجدول الخاص به).
     */
    protected function trainingSchedule(?Reservation $reservation): array
    {
        $slots = $reservation?->time_slots ?: (array) optional($reservation?->schedule)->time_slots;

        if (! is_array($slots) || ! $slots) {
            return [null, null];
        }

        $days  = [];
        $hours = [];

        foreach ($slots as $slot) {
            if (! is_array($slot)) {
                continue;
            }
            $day = (int) ($slot['day_number'] ?? -1);
            if ($day >= 0 && ! in_array($day, $days, true)) {
                $days[] = $day;
            }
            if (! empty($slot['start']) && ! empty($slot['end'])) {
                $text = $slot['start'] . ' - ' . $slot['end'];
                if (! in_array($text, $hours, true)) {
                    $hours[] = $text;
                }
            }
        }

        sort($days);
        $dayNames = collect($days)->map(fn ($d) => $this->dayName($d));

        return [$dayNames->implode('، '), implode(' / ', $hours)];
    }

    /**
     * هل التأمين الصحي للاعب سارٍ؟
     */
    protected function assuranceIsValid(Person $person): bool
    {
        // 1) عبر عمود التأمين في persons (etat_ass = 0/1)
        if ((int) $person->etat_ass === 1 && ! empty($person->assured_expires_on)) {
            $exp = Carbon::parse($person->assured_expires_on);
            $ok  = $exp->gte(now());

            if ($ok) {
                return true;
            }
        }

        // 2) عبر سجلّات التأمين
        $ass = PersonAssurance::where('person_id', $person->id)
            ->orderByDesc('end_date')
            ->first();

        return $ass
            && ! in_array($ass->status, ['cancelled', 'expired', 'انتهى', 'ملغى'], true)
            && Carbon::parse($ass->end_date)->gte(now());
    }

    protected function cardNumber(int $personId, string $label): string
    {
        return 'OPOW-' . str_replace('/', '-', str_replace(' ', '', $label)) . '-' . str_pad((string) $personId, 4, '0', STR_PAD_LEFT);
    }

    protected function dayName(int $day): string
    {
        return match ($day) {
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
            default => '—',
        };
    }

    /**
     * بيانات عامة آمنة لصفحة التحقق فقط.
     */
    public function publicData(string $token): ?array
    {
        $resolved = $this->resolveToken($token);

        if (! $resolved) {
            return null;
        }

        $person = Person::with(['ageCategory', 'club', 'complex'])
            ->whereKey($resolved['personId'])
            ->first();

        $season = $resolved['seasonId'] !== 0 ? Season::find($resolved['seasonId']) : null;

        if (! $person) {
            return null;
        }

        $card = $this->build($person, $season);

        return [
            'cardNumber'  => $card['cardNumber'],
            'seasonLabel' => $card['seasonLabel'],
            'status'      => $card['status'],
            'firstname'   => $card['firstname'],
            'lastname'    => $card['lastname'],
            'category'    => $card['category'],
            'club'        => $card['club'] ?: '—',
            'complex'     => $card['complex'] ?: '—',
            'canIssue'    => $card['canIssue'],
        ];
    }
}
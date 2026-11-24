<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Person;
use App\Models\Reservation;
use App\Models\ReservationCredit;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Parcours réel (routes reservation.store / reservations.renew.store / payment.initiate / destroy).
 * Saison 2037 = nov. 2026 (prorata), 2038 = déc. 2026 (prix plein). À lancer sur une base LOCALE.
 */
class ReservationCreditFlowTest extends TestCase
{
    use DatabaseTransactions;

    private const NOV = 2037;
    private const DEC = 2038;
    private const CA  = 67;

    private static array $report = [];

    private function user(array $credits): array
    {
        $user = User::create(['name' => 'T ' . uniqid(), 'email' => uniqid('t') . '@example.test',
            'password' => bcrypt('x'), 'type' => 'person']);
        $person = Person::create(['user_id' => $user->id, 'firstname' => 'T', 'lastname' => 'T',
            'birth_date' => '1990-01-01', 'etat_ass' => 1]);

        $origin = Reservation::create(['user_id' => $user->id, 'total_price' => 5000,
            'statut' => 'confirmee', 'payment_status' => 'paid']);
        foreach ($credits as $i => $a) {
            ReservationCredit::create(['reservation_id' => $origin->id, 'user_id' => $user->id,
                'closure_date' => '2026-08-' . (10 + $i), 'credited_amount' => $a, 'status' => 'pending']);
        }

        return [$user, $person];
    }

    private function schedule(float $price): Schedule
    {
        return Schedule::create([
            'complex_activity_id' => self::CA, 'groupe' => 'test', 'price' => $price, 'active' => 1,
            'type_prix' => 'fix', 'type_season' => 'monthly',
            'time_slots' => json_encode([
                ['day_number' => 1, 'start' => '12:00', 'end' => '13:00'],
                ['day_number' => 3, 'start' => '12:00', 'end' => '13:00'],
            ]),
        ]);
    }

    private function book(User $u, Person $p, Schedule $s, int $season)
    {
        return $this->actingAs($u)->post(route('reservation.store'), [
            'complex_activity_id' => self::CA, 'season_id' => $season,
            'schedule_id' => $s->id, 'person_id' => $p->id,
        ]);
    }

    private function last(User $u, int $season): Reservation
    {
        return Reservation::where('user_id', $u->id)->where('season_id', $season)->latest('id')->firstOrFail();
    }

    private function creditSum(User $u, string $kind): float
    {
        $q = ReservationCredit::where('user_id', $u->id);
        if ($kind === 'used') {
            $q->where('status', 'used');
        } elseif ($kind === 'reserved') {
            $q->where('status', 'pending')->whereNotNull('used_in_reservation_id');
        } else {
            $q->where('status', 'pending')->whereNull('used_in_reservation_id');
        }

        return (float) $q->sum('credited_amount');
    }

    private function row(string $sc, string $in, Reservation $r, User $u): void
    {
        $r = $r->fresh();
        self::$report[] = sprintf('%s | %s | %s/%s | a payer=%s (remise=%s) | credits utilises=%s reserves=%s libres=%s | note=%s',
            $sc, $in, $r->statut, $r->payment_status, number_format($r->total_price, 2, '.', ''),
            $r->credit_discount, $this->creditSum($u, 'used'), $this->creditSum($u, 'reserved'),
            $this->creditSum($u, 'free'), $r->credit_note ?: '-');
    }

    protected function tearDown(): void
    {
        if (self::$report) {
            fwrite(STDERR, "\n" . implode("\n", self::$report) . "\n");
            self::$report = [];
        }
        parent::tearDown();
    }

    /** Clic sur « payer » (PaymentController::initiate) avec passerelle simulée. */
    private function pay(User $u, Reservation $r)
    {
        Http::fake([
            'www.google.com/*'   => Http::response(['success' => true]),
            'epay.guiddini.dz/*' => Http::response(['data' => ['id' => 'ORD' . uniqid(),
                'attributes' => ['form_url' => 'https://pay.test/x']]]),
        ]);

        return $this->actingAs($u)->post(route('payment.initiate'), [
            'reservation_id' => $r->id, 'g-recaptcha-response' => 'x', 'accept_terms' => 1,
        ]);
    }

    private function gatewayCalls(): int
    {
        return Http::recorded(fn ($req) => str_contains($req->url(), 'guiddini'))->count();
    }

    public function test_s1_credits_exceed_price(): void
    {
        [$u, $p] = $this->user([500, 500, 500, 500, 500]);
        $this->book($u, $p, $this->schedule(2000), self::DEC)->assertSessionHasNoErrors();
        $r = $this->last($u, self::DEC);
        $this->row('S1 creation', 'credits 5x500=2500, mois 2000', $r, $u);
        $this->assertSame('pending', $r->payment_status);
        $this->assertEquals(0.0, (float) $r->total_price);
        $this->assertEquals(2000.0, (float) $r->credit_discount);

        $this->pay($u, $r)->assertSessionHas('success');
        $r = $r->fresh();
        $this->row('S1 apres payer', '', $r, $u);
        $this->assertSame('paid', $r->payment_status);
        $this->assertSame('confirmee', $r->statut);
        $this->assertEquals(500.0, $this->creditSum($u, 'free'));
        $this->assertSame(0, $this->gatewayCalls());
    }

    public function test_s2_credits_equal_price(): void
    {
        [$u, $p] = $this->user([1000, 1000]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);
        $this->row('S2 creation', 'credits 2x1000, mois 2000', $r, $u);
        $this->assertSame('pending', $r->payment_status);
        $this->pay($u, $r);
        $this->row('S2 apres payer', '', $r, $u);
        $this->assertSame('paid', $r->fresh()->payment_status);
        $this->assertSame(0, $this->gatewayCalls());
    }

    public function test_s3_remainder_below_one_dinar_mimouni_case(): void
    {
        [$u, $p] = $this->user([222.22, 222.22, 222.22]);
        $this->book($u, $p, $this->schedule(3000), self::NOV);
        $r = $this->last($u, self::NOV);
        $this->row('S3 creation', 'credits 3x222.22=666.66, nov (prorata 666.67)', $r, $u);
        $this->assertSame('pending', $r->payment_status);
        $this->assertEquals(0.01, (float) $r->total_price);
        $this->pay($u, $r);
        $this->row('S3 apres payer', '', $r, $u);
        $this->assertSame('paid', $r->fresh()->payment_status);
        $this->assertSame(0, $this->gatewayCalls());
    }

    public function test_s4_partial_cover_goes_to_gateway(): void
    {
        [$u, $p] = $this->user([500, 500]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);
        $this->row('S4 creation', 'credits 2x500=1000, mois 2000', $r, $u);
        $this->assertSame('pending', $r->payment_status);
        $this->assertEquals(1000.0, (float) $r->total_price);
        $this->assertEquals(1000.0, (float) $r->credit_discount);

        $this->pay($u, $r);
        $this->row('S4 apres payer', '', $r, $u);
        $r = $r->fresh();
        $this->assertNull($r->credit_note);
        $this->assertNotSame('paid', $r->payment_status);
        Http::assertSent(fn ($req) => str_contains($req->url(), 'payment/initiate') && (int) $req['amount'] === 1000);
    }

    public function test_s5_no_credit_normal_flow(): void
    {
        [$u, $p] = $this->user([]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);
        $this->row('S5 creation', 'aucun credit, mois 2000', $r, $u);
        $this->assertSame('pending', $r->payment_status);
        $this->assertEquals(2000.0, (float) $r->total_price);

        $this->pay($u, $r);
        $this->assertGreaterThan(0, $this->gatewayCalls());
        $this->assertNotSame('paid', $r->fresh()->payment_status);
        $this->row('S5 apres payer', '(passerelle appelee, 2000)', $r, $u);
    }

    public function test_s6_two_months_in_a_row_same_balance(): void
    {
        [$u, $p] = $this->user([222.22, 222.22, 222.22]);
        $s = $this->schedule(3000);
        $this->book($u, $p, $s, self::NOV);
        $this->book($u, $p, $s, self::DEC);
        $nov = $this->last($u, self::NOV);
        $dec = $this->last($u, self::DEC);
        $this->row('S6a creation nov', '', $nov, $u);
        $this->row('S6b creation dec', '', $dec, $u);
        $this->assertSame('pending', $nov->payment_status);
        $this->assertSame('pending', $dec->payment_status);
        $this->assertEquals(0.01, (float) $nov->total_price);
        $this->assertEquals(3000.0, (float) $dec->total_price);

        $this->pay($u, $nov);
        $this->assertSame('paid', $nov->fresh()->payment_status);
        $this->assertSame('pending', $dec->fresh()->payment_status);
        $this->row('S6a apres payer nov', '', $nov, $u);
        $this->row('S6b dec inchange', '', $dec, $u);
    }

    public function test_s7_remark_free_payment_and_person_state(): void
    {
        [$u, $p] = $this->user([500, 500, 500, 500]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);
        $this->assertNull($r->credit_note);
        $this->pay($u, $r);
        $r = $r->fresh();
        $ids = ReservationCredit::where('used_in_reservation_id', $r->id)->orderBy('id')->pluck('id')->implode(', ');
        $this->row('S7 apres payer', 'credits 4x500, mois 2000', $r, $u);
        $this->assertSame("مدفوع بالتعويض : 2000.00 دج (الأرصدة رقم $ids)", $r->credit_note);
        $pay = Payment::find($r->payment_id);
        $this->assertNotNull($pay);
        $this->assertStringStartsWith('FREE-', $pay->order_id);
        $this->assertEquals(0, (float) $pay->amount);
        $this->assertSame('success', $pay->status);
        $this->assertSame(0, (int) $p->fresh()->etat_ass);
        self::$report[] = 'S7 | paiement ' . $pay->order_id . ' montant ' . $pay->amount . ' ' . $pay->status
            . ' ; etat_ass 1 -> ' . $p->fresh()->etat_ass;
    }

    public function test_s8_delete_reservation_paid_by_credits(): void
    {
        [$u, $p] = $this->user([500, 500, 500, 500]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);
        $this->pay($u, $r);
        $this->actingAs($u)->delete(route('reservations.destroy', $r->id));
        $this->assertNull(Reservation::find($r->id));
        $free = $this->creditSum($u, 'free');
        self::$report[] = 'S8 | suppression de la reservation payee par remboursement | credits libres apres = '
            . $free . ' / 2000 ; utilises = ' . $this->creditSum($u, 'used');
        $this->assertEquals(2000.0, $free);
    }

    public function test_s8b_delete_pending_reservation_releases_reserved_credits(): void
    {
        [$u, $p] = $this->user([500, 500]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);
        $this->assertEquals(1000.0, $this->creditSum($u, 'reserved'));
        $this->actingAs($u)->delete(route('reservations.destroy', $r->id));
        $this->assertEquals(1000.0, $this->creditSum($u, 'free'));
        $this->assertEquals(0.0, $this->creditSum($u, 'reserved'));
    }

    public function test_s9_double_click_on_pay_is_idempotent(): void
    {
        [$u, $p] = $this->user([500, 500, 500, 500, 500]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);

        $this->pay($u, $r);
        $first = $r->fresh();
        $usedAfterFirst = $this->creditSum($u, 'used');
        $paymentsBefore = Payment::count();

        $second = $this->pay($u, $r);
        $second->assertSessionHas('info');

        $this->assertEquals($usedAfterFirst, $this->creditSum($u, 'used'));
        $this->assertEquals(500.0, $this->creditSum($u, 'free'));
        $this->assertSame($paymentsBefore, Payment::count());
        $this->assertSame($first->payment_id, $r->fresh()->payment_id);
        $this->assertSame($first->credit_note, $r->fresh()->credit_note);
        $this->assertSame(0, $this->gatewayCalls());
        self::$report[] = 'S9 | double clic sur payer | 2e clic: message info, paiements +0, credits utilises inchanges (' . $usedAfterFirst . ')';
    }

    public function test_s10_user_cannot_pay_someone_elses_reservation(): void
    {
        [$u, $p] = $this->user([500, 500, 500, 500]);
        [$other] = $this->user([]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);

        $this->pay($other, $r)->assertStatus(403);
        $this->assertSame('pending', $r->fresh()->payment_status);
    }

    public function test_zero_price_without_credit_is_never_paid_by_click(): void
    {
        [$u, $p] = $this->user([]);
        $r = Reservation::create(['user_id' => $u->id, 'total_price' => 0, 'statut' => 'en_attente', 'payment_status' => 'pending']);
        $this->pay($u, $r);
        $this->assertNotSame('paid', $r->fresh()->payment_status);
    }

    public function test_renew_store_reserves_then_pay_settles(): void
    {
        [$u, $p] = $this->user([500, 500, 500, 500]);
        $s = $this->schedule(2000);
        $old = Reservation::create(['user_id' => $u->id, 'person_id' => $p->id, 'schedule_id' => $s->id,
            'season_id' => self::NOV, 'complex_activity_id' => self::CA, 'start_date' => '2026-11-01',
            'end_date' => '2026-11-30', 'time_slots' => json_decode($s->time_slots, true), 'total_price' => 100,
            'statut' => 'confirmee', 'payment_status' => 'paid', 'qty_places' => 1]);
        $this->actingAs($u)->post(route('reservations.renew.store', $old->id), ['season_id' => self::DEC]);
        $r = $this->last($u, self::DEC);
        $this->row('RN creation', 'renouvellement, credits 2000, dec 2000', $r, $u);
        $this->assertSame('pending', $r->payment_status);
        $this->assertEquals(0.0, (float) $r->total_price);
        $this->pay($u, $r);
        $this->row('RN apres payer', '', $r, $u);
        $this->assertSame('paid', $r->fresh()->payment_status);
    }

    public function test_threshold_comes_from_a_single_config(): void
    {
        $this->assertSame(100.0, \App\Services\ReservationCreditService::threshold());
    }

    public function test_remaining_under_threshold_shows_credit_button_and_settles_with_waived_note(): void
    {
        [$u, $p] = $this->user([1970]);                       // reste 30 sur un mois de 2000
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);
        $this->assertEquals(30.0, (float) $r->total_price);

        $html = $this->actingAs($u)->get(route('reservation.my-reservations'))->getContent();
        $this->assertStringContainsString('الحجز بالتعويض', $html);
        $this->assertStringContainsString(route('payments.credit', $r->id), $html);

        Http::fake();
        $this->actingAs($u)->post(route('payments.credit', $r->id))->assertSessionHas('success');
        $r = $r->fresh();
        $this->assertSame('paid', $r->payment_status);
        $this->assertSame('confirmee', $r->statut);
        $this->assertEquals(0.0, (float) $r->total_price);
        $this->assertEquals(30.0, (float) $r->credit_waived);
        $this->assertEquals(1970.0, (float) $r->credit_discount);
        $this->assertStringEndsWith('(باقي 30.00 دج مُسقَط)', $r->credit_note);
        $this->assertSame(0, $this->gatewayCalls());
        $this->row('SEUIL reste 30', 'credit 1970, mois 2000', $r, $u);
    }

    public function test_remaining_99_99_settles_but_100_goes_to_gateway(): void
    {
        [$u1, $p1] = $this->user([1900.01]);
        $this->book($u1, $p1, $this->schedule(2000), self::DEC);
        $r1 = $this->last($u1, self::DEC);
        $this->assertEquals(99.99, (float) $r1->total_price);
        $this->assertTrue($r1->canPayByCredit());
        $this->actingAs($u1)->post(route('payments.credit', $r1->id));
        $this->assertSame('paid', $r1->fresh()->payment_status);

        [$u2, $p2] = $this->user([1900]);
        $this->book($u2, $p2, $this->schedule(2000), self::DEC);
        $r2 = $this->last($u2, self::DEC);
        $this->assertEquals(100.0, (float) $r2->total_price);
        $this->assertFalse($r2->canPayByCredit());
        $html = $this->actingAs($u2)->get(route('reservation.my-reservations'))->getContent();
        $this->assertStringNotContainsString(route('payments.credit', $r2->id), $html);
        $this->assertStringContainsString(route('payments.pay', $r2->id), $html);

        $this->actingAs($u2)->post(route('payments.credit', $r2->id))->assertSessionHas('error');
        $this->assertSame('pending', $r2->fresh()->payment_status);

        $this->pay($u2, $r2);                       // la passerelle est appelee pour le reste (100)
        Http::assertSent(fn ($req) => str_contains($req->url(), 'payment/initiate') && (int) $req['amount'] === 100);
    }

    public function test_small_price_without_credit_is_never_settled_by_credit(): void
    {
        [$u, $p] = $this->user([]);
        $r = Reservation::create(['user_id' => $u->id, 'total_price' => 10, 'statut' => 'en_attente', 'payment_status' => 'pending']);
        $this->assertFalse($r->canPayByCredit());
        $this->actingAs($u)->post(route('payments.credit', $r->id))->assertSessionHas('error');
        $this->assertSame('pending', $r->fresh()->payment_status);
    }

    public function test_price_detail_lines_are_displayed_once_each(): void
    {
        [$u, $p] = $this->user([500, 500]);                    // 2000 - 1000
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $html = $this->actingAs($u)->get(route('reservation.my-reservations'))->getContent();

        // une ligne par information, dans le tableau ET dans la carte mobile (2 rendus = 2 occurrences)
        foreach (['عدد حصص الشهر', 'الحصص المحسوبة', 'سعر الحصة', 'قيمة الحصص',
                  'الرصيد التعويضي الإجمالي', 'الخصم من هذا الحجز', 'الرصيد المتبقي', 'المتبقي للدفع'] as $label) {
            $this->assertSame(2, substr_count($html, $label), $label);
        }
        $this->assertStringContainsString('2000.00', $html);
        $this->assertStringContainsString('1000.00', $html);
    }

    public function test_mimouni_case_session_value_and_balances(): void
    {
        \Carbon\Carbon::setTestNow('2026-11-24 11:00:00');   // reservation du 24/11 : 2 seances restantes sur 9
        try {
            [$u, $p] = $this->user([222.22, 222.22, 222.22]);
            $this->book($u, $p, $this->schedule(2000), self::NOV);
            $r = $this->last($u, self::NOV);

            $d = $r->price_detail;
            $this->assertSame(9, $d['sessions_total']);
            $this->assertSame(2, $d['sessions_counted']);
            $this->assertEquals(222.22, $d['unit_price']);
            $this->assertEquals(444.44, $d['sessions_value']);
            $this->assertEquals(666.66, $d['credit_balance_before']);
            $this->assertEquals(222.22, $d['credit_balance_after']);
            $this->assertEquals(444.44, (float) $r->credit_discount);
            $this->assertEquals(0.0, (float) $r->total_price);

            $html = $this->actingAs($u)->get(route('reservation.my-reservations'))->getContent();
            foreach ([
                'عدد حصص الشهر : <span dir="ltr">9</span>',
                'الحصص المحسوبة : <span dir="ltr">2</span>',
                'سعر الحصة : <span dir="ltr">222.22</span> دج',
                'قيمة الحصص : <span dir="ltr">444.44</span> دج',
                'الرصيد التعويضي الإجمالي : <span dir="ltr">666.66</span> دج',
                'الخصم من هذا الحجز : <span dir="ltr">444.44</span> دج',
                'الرصيد المتبقي : <span dir="ltr">222.22</span> دج',
                'المتبقي للدفع : <span dir="ltr">0.00</span> دج',
            ] as $line) {
                $this->assertStringContainsString($line, $html);
            }

            // detail fige : un solde modifie apres coup ne change pas l'affichage
            ReservationCredit::where('user_id', $u->id)->whereNull('used_in_reservation_id')->update(['credited_amount' => 1]);
            $html2 = $this->actingAs($u)->get(route('reservation.my-reservations'))->getContent();
            $this->assertStringContainsString('الرصيد المتبقي : <span dir="ltr">222.22</span> دج', $html2);
        } finally {
            \Carbon\Carbon::setTestNow();
        }
    }

    public function test_no_credit_shows_sessions_but_no_balance_lines(): void
    {
        [$u, $p] = $this->user([]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $html = $this->actingAs($u)->get(route('reservation.my-reservations'))->getContent();
        $this->assertStringContainsString('قيمة الحصص', $html);
        $this->assertStringNotContainsString('الرصيد التعويضي الإجمالي', $html);
        $this->assertStringNotContainsString('الخصم من هذا الحجز', $html);
    }

    public function test_legacy_reservation_without_detail_renders_without_error(): void
    {
        [$u, $p] = $this->user([]);
        $r = Reservation::create(['user_id' => $u->id, 'person_id' => $p->id, 'total_price' => 1500,
            'statut' => 'en_attente', 'payment_status' => 'pending', 'complex_activity_id' => self::CA]);
        $res = $this->actingAs($u)->get(route('reservation.my-reservations'))->assertOk();
        $this->assertStringContainsString('1500.00', $res->getContent());
        $this->assertStringNotContainsString('عدد حصص الشهر', $res->getContent());
    }

    public function test_renewal_does_not_copy_credit_fields_from_old_reservation(): void
    {
        Http::fake();
        [$u, $p] = $this->user([]);
        $s = $this->schedule(2000);
        $old = Reservation::create(['user_id' => $u->id, 'person_id' => $p->id, 'schedule_id' => $s->id,
            'season_id' => self::NOV, 'complex_activity_id' => self::CA, 'start_date' => '2026-11-01',
            'end_date' => '2026-11-30', 'time_slots' => json_decode($s->time_slots, true), 'total_price' => 0,
            'statut' => 'confirmee', 'payment_status' => 'paid', 'qty_places' => 1,
            'credit_discount' => 500, 'credit_waived' => 20, 'credit_note' => 'ancienne remarque']);
        $this->actingAs($u)->post(route('reservations.renew.store', $old->id), ['season_id' => self::DEC]);
        $r = $this->last($u, self::DEC);
        $this->assertEquals(0.0, (float) $r->credit_discount);
        $this->assertEquals(0.0, (float) $r->credit_waived);
        $this->assertNull($r->credit_note);
        $this->assertEquals(2000.0, (float) $r->total_price);
    }

    public function test_payment_page_shows_the_detail_only_to_the_owner(): void
    {
        [$u, $p] = $this->user([500, 500]);
        [$other] = $this->user([700]);
        $this->book($u, $p, $this->schedule(2000), self::DEC);
        $r = $this->last($u, self::DEC);

        $html = $this->actingAs($u)->get(route('payments.pay', $r->id))->assertOk()->getContent();
        foreach (['عدد حصص الشهر', 'قيمة الحصص', 'الرصيد التعويضي الإجمالي', 'الخصم من هذا الحجز', 'الرصيد المتبقي', 'المتبقي للدفع'] as $label) {
            $this->assertSame(1, substr_count($html, $label), $label);
        }
        $this->assertStringContainsString('1000.00', $html);
        $this->assertStringNotContainsString('رقم الرصيد', $html);

        $this->actingAs($other)->get(route('payments.pay', $r->id))->assertForbidden();
    }

    public function test_banner_shows_full_balance_total_used_reserved_available(): void
    {
        [$u, $p] = $this->user([500, 500, 500, 500]);                  // 2000 de credits
        [$other] = $this->user([9999]);                                // ne doit jamais apparaitre

        $used = ReservationCredit::where('user_id', $u->id)->orderBy('id')->first();
        $used->update(['status' => 'used', 'used_in_reservation_id' => Reservation::where('user_id', $u->id)->value('id')]);
        ReservationCredit::where('user_id', $u->id)->where('status', 'pending')->orderBy('id')->first()
            ->update(['used_in_reservation_id' => Reservation::where('user_id', $u->id)->value('id')]);
        ReservationCredit::create(['reservation_id' => Reservation::where('user_id', $u->id)->value('id'), 'user_id' => $u->id,
            'closure_date' => '2026-08-30', 'credited_amount' => 777, 'status' => 'cancelled']);   // annule : ne compte pas

        $s = app(\App\Services\ReservationCreditService::class)->summary($u->id);
        $this->assertEquals(2000.0, $s['total']);
        $this->assertEquals(500.0, $s['used']);
        $this->assertEquals(500.0, $s['reserved']);
        $this->assertEquals(1000.0, $s['available']);
        $this->assertEquals($s['available'], app(\App\Services\ReservationCreditService::class)->availableAmount($u->id));
        $this->assertCount(4, $s['rows']);

        $html = $this->actingAs($u)->get(route('reservation.my-reservations'))->getContent();
        foreach (['إجمالي التعويضات : <span dir="ltr">2000.00</span>', 'المستعمل : <span dir="ltr">500.00</span>',
                  'محجوز لحجوزات قيد الانتظار : <span dir="ltr">500.00</span>', 'المتاح : <span dir="ltr">1000.00</span>',
                  'سيتم خصمه من الحجز القادم', 'تفاصيل التعويضات', '<details'] as $needle) {
            $this->assertStringContainsString($needle, $html);
        }
        $this->assertStringNotContainsString('9999.00', $html);
        $this->assertStringNotContainsString('777.00', $html);
        $this->assertSame(1, substr_count($html, 'تفاصيل التعويضات'));
    }

    public function test_banner_is_hidden_without_credit_and_hides_next_deduction_message_when_nothing_available(): void
    {
        [$u] = $this->user([]);
        $this->assertStringNotContainsString('إجمالي التعويضات', $this->actingAs($u)->get(route('reservation.my-reservations'))->getContent());

        [$v, $p] = $this->user([500]);
        $this->book($v, $p, $this->schedule(2000), self::DEC);          // le seul credit est reserve
        $html = $this->actingAs($v)->get(route('reservation.my-reservations'))->getContent();
        $this->assertStringContainsString('إجمالي التعويضات', $html);
        $this->assertStringNotContainsString('سيتم خصمه من الحجز القادم', $html);
    }

    public function test_my_reservations_are_sorted_most_recent_month_first(): void
    {
        [$u, $p] = $this->user([]);
        $mk = fn (string $start, string $created) => Reservation::create([
            'user_id' => $u->id, 'person_id' => $p->id, 'total_price' => 100, 'statut' => 'confirmee',
            'payment_status' => 'paid', 'start_date' => $start, 'end_date' => date('Y-m-t', strtotime($start)),
            'created_at' => $created,
        ]);

        // ids volontairement dans le desordre chronologique
        $aug  = $mk('2026-08-01', '2026-07-26 15:00:00');
        $nov  = $mk('2026-11-01', '2026-11-02 10:00:00');
        $may  = $mk('2026-05-01', '2026-05-12 12:00:00');
        $nov2 = $mk('2026-11-01', '2026-11-20 09:00:00');         // meme mois, demande plus recente
        $novB = $mk('2026-11-01', '2026-11-20 09:00:00');         // meme mois et meme date : id decroissant

        $html = $this->actingAs($u)->get(route('reservation.my-reservations'))->getContent();
        $pos = fn (Reservation $r) => strpos($html, '<td>' . $r->id . '</td>');

        $expected = [$novB, $nov2, $nov, $aug, $may];
        $positions = array_map($pos, $expected);
        $this->assertNotContains(false, $positions);
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($sorted, $positions);

        // la version mobile (cartes) suit le meme ordre (meme collection)
        $mob = fn (Reservation $r) => strpos($html, '#' . $r->id);
        $m = array_map($mob, $expected);
        $ms = $m;
        sort($ms);
        $this->assertSame($ms, $m);
    }
}

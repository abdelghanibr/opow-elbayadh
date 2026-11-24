<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/** Bascule du super admin vers un autre compte. À lancer sur une base LOCALE. */
class ImpersonationTest extends TestCase
{
    use DatabaseTransactions;

    private function make(string $type, $complexId = null): User
    {
        return User::create([
            'name' => 'T ' . $type . ' ' . uniqid(), 'email' => uniqid('imp') . '@example.test',
            'password' => bcrypt('x'), 'type' => $type, 'complex_id' => $complexId,
        ]);
    }

    public function test_super_admin_can_enter_a_person_account_and_come_back(): void
    {
        $super  = $this->make('admin', 0);
        $person = $this->make('person');

        $this->actingAs($super)->post(route('impersonate.start', $person->id))
            ->assertRedirect(route('person.dashboard'));

        $this->assertAuthenticatedAs($person);
        $this->assertSame($super->id, session('impersonator_id'));

        // journal : table audit_logs si elle existe (sinon fichier de log)
        $hasAudit = Schema::hasTable('audit_logs');
        if ($hasAudit) {
            $start = DB::table('audit_logs')->where('event', 'impersonate_start')->where('user_id', $super->id)->latest('id')->first();
            $this->assertNotNull($start);
            $this->assertSame($person->id, json_decode($start->new_values, true)['to']['id']);
            $this->assertNotEmpty($start->created_at);
        }

        $this->post(route('impersonate.stop'))->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($super);
        $this->assertNull(session('impersonator_id'));
        if ($hasAudit) {
            $this->assertNotNull(DB::table('audit_logs')->where('event', 'impersonate_stop')->where('user_id', $super->id)->first());
        }
    }

    public function test_super_admin_can_enter_club_company_and_complex_admin(): void
    {
        $super = $this->make('admin', 0);

        foreach ([['club', 'club.dashboard'], ['entreprise', 'entreprise.dashboard'], ['admin', 'admin.dashboard']] as [$type, $route]) {
            $target = $this->make($type, $type === 'admin' ? 13 : null);
            $this->actingAs($super)->post(route('impersonate.start', $target->id))->assertRedirect(route($route));
            $this->assertAuthenticatedAs($target);
            $this->post(route('impersonate.stop'));
            $this->assertAuthenticatedAs($super);
        }
    }

    public function test_complex_admin_and_client_cannot_impersonate(): void
    {
        $target = $this->make('person');

        $this->actingAs($this->make('admin', 13))->post(route('impersonate.start', $target->id))->assertForbidden();
        $this->actingAs($this->make('person'))->post(route('impersonate.start', $target->id))->assertForbidden();
        $this->actingAs($this->make('financier'))->post(route('impersonate.start', $target->id))->assertForbidden();
        $this->actingAs($this->make('admin', 13))->getJson(route('impersonate.users'))->assertForbidden();
    }

    public function test_guest_is_redirected(): void
    {
        $target = $this->make('person');
        $this->post(route('impersonate.start', $target->id))->assertRedirect();
        $this->assertGuest();
    }

    public function test_cannot_chain_two_switches(): void
    {
        $super = $this->make('admin', 0);
        $a = $this->make('person');
        $b = $this->make('person');

        $this->actingAs($super)->post(route('impersonate.start', $a->id));
        $this->post(route('impersonate.start', $b->id))->assertForbidden();
        $this->assertAuthenticatedAs($a);
    }

    public function test_cannot_enter_self_other_super_admin_or_unknown_account(): void
    {
        $super = $this->make('admin', 0);
        $other = $this->make('admin', 0);

        $this->actingAs($super)->post(route('impersonate.start', $super->id))->assertForbidden();
        $this->post(route('impersonate.start', $other->id))->assertForbidden();
        $this->post(route('impersonate.start', 999999999))->assertForbidden();
        $this->assertAuthenticatedAs($super);
        $this->assertNull(session('impersonator_id'));
    }

    public function test_stop_is_refused_without_a_switch_session(): void
    {
        $person = $this->make('person');
        $this->actingAs($person)->post(route('impersonate.stop'))->assertForbidden();

        $super = $this->make('admin', 0);
        $this->actingAs($super)->post(route('impersonate.stop'))->assertForbidden();
    }

    public function test_banner_is_shown_only_during_a_switch(): void
    {
        $super  = $this->make('admin', 0);
        $person = $this->make('person');

        $this->actingAs($super)->post(route('impersonate.start', $person->id));
        $html = $this->get(route('person.dashboard'))->getContent();
        $this->assertStringContainsString('impersonation-banner', $html);
        $this->assertStringContainsString('أنت متصل الآن بحساب', $html);
        $this->assertStringContainsString(route('impersonate.stop'), $html);

        $this->post(route('impersonate.stop'));
        $this->assertStringNotContainsString('impersonation-banner', $this->get(route('admin.dashboard'))->getContent());
    }

    public function test_user_search_json_excludes_super_admins_and_self(): void
    {
        $super = $this->make('admin', 0);
        $other = $this->make('admin', 0);
        $person = $this->make('person');

        $res = $this->actingAs($super)->getJson(route('impersonate.users', ['q' => $person->email]))->assertOk();
        $ids = collect($res->json())->pluck('id');
        $this->assertTrue($ids->contains($person->id));

        $ids = collect($this->getJson(route('impersonate.users', ['q' => 'example.test']))->json())->pluck('id');
        $this->assertFalse($ids->contains($super->id));
        $this->assertFalse($ids->contains($other->id));
    }

    public function test_switch_does_not_trigger_login_side_effects(): void
    {
        $super  = $this->make('admin', 0);
        $admin2 = $this->make('admin', 13);

        $this->actingAs($super)->post(route('impersonate.start', $admin2->id));
        // aucun événement Login n'est émis par la bascule (ni sauvegarde, ni journal de connexion)
        \Illuminate\Support\Facades\Event::fake([\Illuminate\Auth\Events\Login::class]);
        $this->post(route('impersonate.stop'));
        \Illuminate\Support\Facades\Event::assertNotDispatched(\Illuminate\Auth\Events\Login::class);
        $this->assertAuthenticatedAs($super);
    }
}

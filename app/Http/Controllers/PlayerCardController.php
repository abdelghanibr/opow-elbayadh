<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Club;
use App\Models\Complex;
use App\Services\PlayerCardService;

class PlayerCardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $person = Person::where('user_id', $user->id)->first();

        if (! $person) {
            return redirect()->route('person.dashboard')
                ->with('error', 'أكمل بياناتك الشخصية أولاً لإصدار بطاقة اللاعب.');
        }

        $card = (new PlayerCardService)->build($person);

        return view('person.card', compact('card'));
    }

    /**
     * بطاقة أحد الأبناء (فقط لمن هم تحت حساب الوالد).
     */
    public function showChild(Request $request, Person $person)
    {
        $user = $request->user();

        // منع الوصول لبطاقات الآخرين (IDOR)
        $owner = $person->ownerUser();

        abort_unless($owner && (int) $owner->id === (int) $user->id, 403);

        $card = (new PlayerCardService)->build($person);

        return view('person.card', compact('card'));
    }

    /**
     * بطاقة أحد أعضاء النادي / المؤسسة (للنادي أو المؤسسة فقط).
     */
    public function memberCard(Request $request, Person $person)
    {
        $user = $request->user();

        abort_unless(in_array($user->type, ['club', 'company']), 403);

        // منع الوصول لبطاقات غير تابعة للحساب (IDOR)
        abort_unless((int) $person->user_id === (int) $user->id, 403);

        $card = (new PlayerCardService)->build($person);

        $backUrl = $user->type === 'company'
            ? route('entreprise.persons.index')
            : route('club.persons.index');

        return view('person.card', [
            'card'    => $card,
            'backUrl' => $backUrl,
        ]);
    }

    /**
     * طباعة كل بطاقات النادي / المؤسسة في ملف واحد (toutes les cartes en un seul PDF).
     */
    public function cards(Request $request)
    {
        $user = $request->user();

        abort_unless(in_array($user->type, ['club', 'company']), 403);

        $persons = Person::with(['ageCategory', 'club.activity', 'lastReservation.complexActivity.activity', 'complex'])
            ->where('user_id', $user->id)
            ->orderBy('lastname')
            ->get();

        $service = new PlayerCardService;

        $cards = $persons->map(function (Person $p) use ($service) {
            return $service->build($p);
        });

        $entity = ($user->type === 'company'
            ? optional($user->club)->nom
            : optional($user->club)->nom) ?: ($user->type === 'company' ? 'المؤسسة' : 'النادي');

        $backUrl = $user->type === 'company'
            ? route('entreprise.persons.index')
            : route('club.persons.index');

        return view('person.cards-bulk', [
            'cards'       => $cards,
            'entityName'  => $entity,
            'seasonLabel' => $service->seasonLabel(),
            'backUrl'     => $backUrl,
        ]);
    }

    /**
     * 🖨️ بطاقة شخص معيّن للإدارة (أي نوع: فرد / نادٍ / مؤسسة).
     */
    public function adminCard(Request $request, Person $person)
    {
        $user = $request->user();

        abort_unless(in_array($user->type, ['admin', 'verificateur', 'financier']), 403);

        // 🔐 نطاق مجمع الإدارة : إذا كان مرتبطًا بمجمع، يرى فقط أشخاص هذا المجمع
        if (! empty($user->complex_id)) {
            $inComplex = (int) ($person->complex_id ?? 0) === (int) $user->complex_id
                || optional($person->user)->complex_id == $user->complex_id
                || optional(optional($person->club)->user)->complex_id == $user->complex_id;

            abort_unless($inComplex, 403, 'هذا الشخص لا ينتمي إلى مجمعك.');
        }

        $card = (new PlayerCardService)->build($person);

        return view('person.card', [
            'card'    => $card,
            'backUrl' => route('persons.index'),
        ]);
    }

    /**
     * 🖨️ صفحة اختيار بطاقات الإدارة (بحث حسب نادٍ / مؤسسة / فرد، وتحديد المطلوب طباعته).
     */
    public function adminCards(Request $request)
    {
        $user = $request->user();

        abort_unless(in_array($user->type, ['admin', 'verificateur', 'financier']), 403);

        $query = Person::with(['ageCategory', 'club.activity', 'lastReservation.complexActivity.activity', 'complex', 'user'])
            ->whereNotNull('firstname')
            ->whereNotNull('lastname')
            ->where('firstname', '!=', '')
            ->where('lastname', '!=', '')
            ->whereHas('user', function ($q) {
                $q->whereIn('type', ['person', 'club', 'company']);
            })
            // 📂 Éligibilité : dossier validé (individu → table dossiers ; membre club/entreprise → table clubs)
            ->where(function ($q) {
                $q->where(function ($q1) {
                    $q1->whereHas('user', fn ($u) => $u->where('type', 'person'))
                       ->whereHas('dossier', fn ($d) => $d->where('etat', 'approved'));
                })
                ->orWhere(function ($q2) {
                    $q2->whereHas('user', fn ($u) => $u->whereIn('type', ['club', 'company']))
                       ->whereHas('user', fn ($u) => $u->whereHas('club', fn ($c) => $c->where('etat', 'approved')));
                });
            });

        // 🔐 نطاق مجمع الإدارة
        if (! empty($user->complex_id)) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('user', function ($q2) use ($user) {
                    $q2->where('complex_id', $user->complex_id);
                })
                // 🏊 membres de club / entreprise liés au complexe via club_id
                ->orWhereHas('club', function ($q2) use ($user) {
                    $q2->whereHas('user', fn ($u) => $u->where('complex_id', $user->complex_id));
                })
                // direct complex_id sur la personne
                ->orWhereHas('complex', fn ($q2) => $q2->where('id', $user->complex_id));
            });
        }

        // 🎯 فلتر النوع (فرد / نادٍ / مؤسسة)
        $typeFilter = $request->input('type');
        if (in_array($typeFilter, ['person', 'club', 'company'], true)) {
            $query->whereHas('user', fn ($q) => $q->where('type', $typeFilter));
        }

        // 🎯 فلتر المنشأة / النادي (club_id)
        $clubFilter = $request->input('club');
        if ($clubFilter) {
            $query->where('club_id', $clubFilter);
        }

        // 🎯 فلتر المركب (complex_id)
        $complexFilter = $request->input('complex');
        if ($complexFilter) {
            $query->where(function ($q) use ($complexFilter) {
                $q->whereHas('user', function ($q2) use ($complexFilter) {
                    $q2->where('complex_id', $complexFilter);
                })
                ->orWhereHas('club', function ($q2) use ($complexFilter) {
                    $q2->whereHas('user', fn ($u) => $u->where('complex_id', $complexFilter));
                })
                ->orWhere('complex_id', $complexFilter);
            });
        }

        // 🔍 بحث نصي (الاسم الكامل)
        $search = trim((string) $request->input('q'));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        $persons = $query->orderByDesc('id')->get();

        // 📌 قائمة الأندية والمؤسسات للفلترة (كلها لها club_id في جدول persons)
        $clubs = Club::whereHas('members')
            ->orderBy('nom')
            ->get(['id', 'nom', 'entity_type']);

        // 🏟️ قائمة المركبات للفلترة
        $complexes = Complex::orderBy('nom')->get(['id', 'nom']);

        return view('admin.cards.index', [
            'persons'       => $persons,
            'clubs'         => $clubs,
            'complexes'     => $complexes,
            'typeFilter'    => $typeFilter,
            'clubFilter'    => $clubFilter,
            'complexFilter' => $complexFilter,
            'search'        => $search,
        ]);
    }

    /**
     * 🖨️ طباعة البطاقات المختارة (ids → fichier واحد).
     */
    public function adminCardsPrint(Request $request)
    {
        $user = $request->user();

        abort_unless(in_array($user->type, ['admin', 'verificateur', 'financier']), 403);

        $ids = $request->input('ids');

        if (! $ids) {
            return redirect()->route('admin.cards')->with('warning', 'الرجاء اختيار شخص واحد على الأقل.');
        }

        $idsArray = array_filter(explode(',', $ids));

        if (! $idsArray) {
            return redirect()->route('admin.cards')->with('warning', 'الرجاء اختيار شخص واحد على الأقل.');
        }

        $persons = Person::with(['ageCategory', 'club.activity', 'lastReservation.complexActivity.activity', 'complex'])
            ->whereIn('id', $idsArray)
            ->orderByDesc('id')
            ->get();

        $service = new PlayerCardService;

        $cards = $persons->map(function (Person $p) use ($service) {
            return $service->build($p);
        });

        return view('person.cards-bulk', [
            'cards'       => $cards,
            'entityName'  => 'البطاقات المحددة',
            'seasonLabel' => $service->seasonLabel(),
            'backUrl'     => route('admin.cards'),
        ]);
    }

    /**
     * صفحة تحقق عامة (بيانات عامة فقط).
     */
    public function verify($token)
    {
        $data = (new PlayerCardService)->publicData($token);

        abort_unless($data, 404);

        return view('person.card-verify', ['card' => $data]);
    }
}
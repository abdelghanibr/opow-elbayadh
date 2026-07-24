<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DossierController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComplexeController;
use App\Http\Controllers\Admin\ComplexController;
use App\Http\Controllers\Admin\ActivitysController;
use App\Http\Controllers\Admin\PricingsPlanController ;
use App\Http\Controllers\Admin\CapacityController ;
use App\Http\Controllers\Admin\ScheduleController ;
use App\Http\Controllers\Admin\AgeCategoryController;
 use App\Http\Controllers\Admin\SeasonController;
 use App\Http\Controllers\Admin\PersonsController ;
   use App\Http\Controllers\ChargilyPayController ; 
use App\Http\Controllers\Api\PersonneController;

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ClubAuthController;
use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Auth\PersonAuthController;


use App\Http\Controllers\PersonController;
use App\Http\Controllers\ClubController; 

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;

 use App\Http\Controllers\PricingPlanController ;
 
 use App\Http\Controllers\ClubDossierController;
 use App\Http\Controllers\EntrepriseDossierController;
 use App\Http\Controllers\Admin\ActivityCategoryController;

use App\Http\Controllers\NewsController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Admin\SeatTypeController;
use App\Http\Controllers\Admin\ComplexSeatController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\DeviceController;

use App\Http\Controllers\Api\RemotePersonController;

use App\Http\Controllers\PoolClosureController;



use Illuminate\Support\Facades\Response;
use App\Models\News;
use App\Models\Event;
use App\Models\Activity;

Route::get('/sitemap.xml', function () {
    $news = class_exists(News::class)
        ? News::query()->latest('updated_at')->take(500)->get()
        : collect();

    $events = class_exists(Event::class)
        ? Event::query()->latest('updated_at')->take(300)->get()
        : collect();

    $activities = class_exists(Activity::class)
        ? Activity::query()->latest('updated_at')->take(300)->get()
        : collect();

    $xml = view('seo.sitemap', compact('news', 'events', 'activities'))->render();

    return Response::make($xml, 200, [
        'Content-Type' => 'application/xml; charset=UTF-8',
    ]);
})->name('sitemap');
















Route::get(
    '/api/complex/{complex}/persons',
    [RemotePersonController::class, 'index']
)->middleware('verify.complex.sig');



Route::view('/legal/terms', 'legal.terms')->name('legal.terms');
Route::view('/legal/privacy', 'legal.privacy')->name('legal.privacy');

Route::post('/payment/initiate', [PaymentController::class, 'initiate'])
    ->name('payment.initiate');

Route::post('/payment/callback', [PaymentController::class, 'callback'])
    ->name('payment.callback');

/*
 | Guiddini / SATIM redirect (GET)
 | يحتوي ?order_number=XXXX
 */
Route::get('/payment/return', [PaymentController::class, 'verify'])
    ->name('payment.verify');

/*
 | صفحة النتيجة النهائية
 */
Route::get('/payment/result', function () {
    return view('payments.result');
})->name('payment.result');


Route::get('/payment/receipt/{orderId}', [PaymentController::class, 'downloadReceipt'])
    ->name('payment.receipt');

Route::post('/payment/receipt/email/{orderId}', [PaymentController::class, 'sendReceiptEmail'])
    ->name('payment.receipt.email');

//Route::get('
//Route::get('/payment/result', [PaymentController::class, 'handleReturn']);

/*
|--------------------------------------------------------------------------
| PAGE D'ACCUEIL PUBLIQUE (SANS AUTH)
|--------------------------------------------------------------------------
*/
Route::get('/personnes', [PersonneController::class, 'index']);
// صفحة تأكيد إعادة تعيين كلمة المرور
Route::get('/home', [HomeController::class, 'index'])->name('home');

//Route::get('/', function () {
  //  return view('welcome');
//})->name('home');
Route::get('/terms-edahabia', function () {
    return view('pages.edahabia-terms');
})->name('terms.edahabia');

Route::get('/', [HomeController::class, 'welcome'])->name('welcome');

Route::get('/complexes/filter/{type}', [HomeController::class, 'filterAjax'])
     ->name('complexes.filter');

 //  Route::get('/complexes', [ComplexeController::class, 'index'])
   //     ->name('complexes.index');

// صفحة تأكيد إعادة تعيين كلمة المرور
//Route::get('/home', [HomeController::class, 'index'])->name('home');

/*Route::get('/', function () {
    return view('welcome');
})->name('home');
*/
Route::get('/tickets/select-seat/{id}', [MatchController::class, 'selectSeat'])
    ->name('tickets.select-seat');

Route::post('/tickets/confirm-pay', [\App\Http\Controllers\TicketPaymentController::class, 'confirmAndPay'])
    ->name('ticket.confirm.pay');

Route::post('/tickets/payment/initiate', [\App\Http\Controllers\TicketPaymentController::class, 'initiatePayment'])
    ->name('ticket.payment.initiate');

Route::get('/tickets/payment/verify', [\App\Http\Controllers\TicketPaymentController::class, 'verify'])
    ->name('ticket.payment.verify');

Route::post('/tickets/confirm', [TicketController::class, 'confirm'])
    ->name('tickets.confirm');


Route::get('/matches/public', [MatchController::class, 'publicMatches'])
     ->name('matches.public');

Route::get('/tickets/reserve/{id}', [TicketController::class, 'reserve'])
     ->name('tickets.reserve');

  Route::get('/events/{id}', [EventController::class, 'show'])
    ->whereNumber('id')
    ->name('events.show');

     Route::get('/news/{id}', [NewsController::class, 'show'])
     ->whereNumber('id')
     ->name('news.show');

/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

  



// Admin
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');


// Club
Route::get('/club/login', [ClubAuthController::class, 'showLogin'])->name('club.login');
Route::post('/club/login', [ClubAuthController::class, 'login'])->name('club.login.post');
Route::get('/club/register', [ClubAuthController::class, 'showRegister'])->name('club.register');
Route::post('/club/register', [ClubAuthController::class, 'register'])->name('club.register.post');
Route::get('/club/dashboard', fn()=>view('club.dashboard'))->middleware('auth')->name('club.dashboard');

// Company
Route::get('/entreprise/login', [CompanyAuthController::class, 'showLogin'])->name('entreprise.login');
Route::post('/entreprise/login', [CompanyAuthController::class, 'login'])->name('entreprise.login.post');
Route::get('/entreprise/register', [CompanyAuthController::class, 'showRegister'])->name('entreprise.register');
Route::post('/entreprise/register', [CompanyAuthController::class, 'register'])->name('entreprise.register.post');
Route::get('/entreprise/dashboard', fn()=>view('entreprise.dashboard'))->middleware('auth')->name('entreprise.dashboard');

// Person
Route::get('/person/login', [PersonAuthController::class, 'showLogin'])->name('person.login');
Route::post('/person/login', [PersonAuthController::class, 'login'])->name('person.login.post');
Route::get('/person/register', [PersonAuthController::class, 'showRegister'])->name('person.register');
Route::post('/person/register', [PersonAuthController::class, 'register'])->name('person.register.post');
Route::get('/person/dashboard', fn()=>view('person.dashboard'))->middleware('auth')->name('person.dashboard');

// Logout Person
Route::post('/person/logout', function () {
    Auth::logout();
    return redirect()->route('person.login');
})->name('person.logout');

// Logout Club
Route::post('/club/logout', function () {
    Auth::logout();
    return redirect()->route('club.login');
})->name('club.logout');

// Logout Company
Route::post('/entreprise/logout', function () {
    Auth::logout();
    return redirect()->route('entreprise.login');
})->name('entreprise.logout');

// Logout Admin
Route::post('/admin/logout', function () {
    Auth::logout();
    return redirect()->route('admin.login');
})->name('admin.logout');

/*
|--------------------------------------------------------------------------
| MOT DE PASSE OUBLIE / RESET
|--------------------------------------------------------------------------
*/
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('/password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');









// ⭐ Dashboard Person
Route::middleware(['auth','person'])->group(function () {
    Route::get('/person/dashboard', [DashboardController::class, 'index'])->name('person.dashboard');

Route::get('/person/profile/edit', [RegisterController::class, 'edit'])->name('person.profile.edit');

 Route::put('/person/profile/update', [RegisterController::class, 'update'])
        ->name('person.profile.update');

Route::get('/dossier/{dossier}/print', 
    [DossierController::class, 'print'])
    ->name('dossier.print');
    
 
     
Route::get('/forms/formulaire/{id}/download', [FormController::class, 'downloadFormulaire'])
    ->name('forms.formulaire.download');

Route::get('/dossiers/{id}/autorisation-parentale/download', [DossierController::class, 'downloadAutorisationParentale'])
    ->name('dossiers.autorisation-parentale.download');
 
 
    
  Route::get('/forms/formulaire/{dossier}', [DossierController::class, 'showFormulaire'])
    ->name('forms.formulaire.view');  
   
  Route::get('/dossiers/{dossier}/autorisation-parentale', [DossierController::class, 'autorisationParentale'])
    ->name('dossiers.autorisation-parentale'); 
    


});

// ⭐ Dashboard Club

Route::middleware(['auth','club'])->group(function ()
 {
    Route::get('/club/dashboard', [DashboardController::class, 'index'])->name('club.dashboard');
  
    Route::get('/club/persons/', [PersonController::class, 'index'])
        ->name('club.persons.index');

   Route::get('/club/persons/edit/{id}', [PersonController::class, 'edit'])
        ->name('club.persons.edit');

    Route::post('/club/persons/update/{id}', [PersonController::class, 'update'])
        ->name('club.persons.update');

    Route::delete('/club/persons/delete/{id}', [PersonController::class, 'destroy'])
        ->name('club.persons.delete');

       
   Route::post('/club/persons/store', [PersonController::class, 'store'])
            ->name('club.persons.store');
        
    Route::get('/club/persons/create', [PersonController::class, 'create'])
        ->name('club.persons.create');
 // profile du club

 Route::get('/club/profile/edit', [RegisterController::class, 'edit'])->name('club.profile.edit');
  Route::put('/club/profile/update', [RegisterController ::class, 'update'])
        ->name('club.profile.update');
        // dossier du club
    Route::get('/club/dossier', [ClubDossierController::class, 'index'])
        ->name('club.dossier.index');

    Route::get('/club/dossier/edit', [ClubDossierController::class, 'edit'])
        ->name('club.dossier.edit');

    Route::put('/club/dossier/update', [ClubDossierController::class, 'update'])
        ->name('club.dossier.update');

 
  //  Route::get('/reservations/select-type', [ReservationController::class, 'selectType'])
     //   ->name('reservation.select_type');
});

// ⭐ Dashboard Entreprise
Route::middleware(['auth','entreprise'])->group(function () {
    Route::get('/entreprise/dashboard', [DashboardController::class, 'index'])->name('entreprise.dashboard');
    
    
    Route::get('/entreprise/persons', [PersonController::class, 'index'])
        ->name('entreprise.persons.index');

    Route::get('/entreprise/persons/edit/{id}', [PersonController::class, 'edit'])
        ->name('entreprise.persons.edit');

    // 📌 تحديث
    Route::post('/entreprise/persons/update/{id}', [PersonController::class, 'update'])
        ->name('entreprise.persons.update');

    // 📌 حذف
    Route::delete('/entreprise/persons/delete/{id}', [PersonController::class, 'destroy'])
        ->name('entreprise.persons.delete');    
        
      Route::post('/entreprise/persons/store', [PersonController::class, 'store'])
            ->name('entreprise.persons.store');
        
    Route::get('/entreprise/persons/create', [PersonController::class, 'create'])
        ->name('entreprise.persons.create'); 
        
        

        Route::get('/entreprise/profile/edit', [RegisterController::class, 'edit'])->name('entreprise.profile.edit');
        Route::put('/entreprise/profile/update', [RegisterController::class, 'update'])
        ->name('entreprise.profile.update');

    Route::get('/entreprise/dossier', [EntrepriseDossierController::class, 'index'])
        ->name('entreprise.dossier.index');

    Route::get('/entreprise/dossier/edit', [EntrepriseDossierController::class, 'edit'])
        ->name('entreprise.dossier.edit');

    Route::put('/entreprise/dossier/update', [EntrepriseDossierController::class, 'update'])
        ->name('entreprise.dossier.update');

});


// ⭐ Dashboard Admin

Route::get('/persons/by-owner/{id}', [PersonController::class, 'byOwner']
)->name('persons.byOwner');    



Route::middleware(['auth','admin'])->group(function () {





Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/pool-closures', [PoolClosureController::class, 'index'])
        ->name('pool-closures.index');

    Route::get('/pool-closures/create', [PoolClosureController::class, 'create'])
        ->name('pool-closures.create');

    Route::post('/pool-closures', [PoolClosureController::class, 'store'])
        ->name('pool-closures.store');

    Route::get('/pool-closures/{poolClosure}', [PoolClosureController::class, 'show'])
        ->name('pool-closures.show');

    Route::get('/pool-closures/{poolClosure}/edit', [PoolClosureController::class, 'edit'])
        ->name('pool-closures.edit');

    Route::put('/pool-closures/{poolClosure}', [PoolClosureController::class, 'update'])
        ->name('pool-closures.update');

    Route::delete('/pool-closures/{poolClosure}', [PoolClosureController::class, 'destroy'])
        ->name('pool-closures.destroy');

    Route::post('/pool-closures/{poolClosure}/apply', [PoolClosureController::class, 'apply'])
        ->name('pool-closures.apply');
});





Route::post('/reservations/{reservation}/toggle-payment', 
    [ReservationController::class, 'togglePayment'])
    ->name('reservations.togglePayment');



     Route::resource('devices', DeviceController::class);

     Route::get('/devices/{device}/connect', [DeviceController::class, 'connect'])
     ->name('devices.connect');    
Route::get(
    'devices/{device}/zk-users',
    [DeviceController::class, 'fetchZkAttendance']
)->name('devices.zkUsers');

Route::post(
    'devices/{device}/import-selected-users',
    [DeviceController::class, 'importSelectedUsers']
)->name('devices.importSelectedUsers');



Route::resource('seat_types', SeatTypeController::class);


        // ---------------------------
        //    COMPLEX SEATS CRUD
        // ---------------------------
        Route::resource('complex_seats', ComplexSeatController::class);


        // ---------------------------
        //         TEAMS CRUD
        // ---------------------------
        Route::resource('teams', TeamController::class);


        // ---------------------------
        //         MATCHES CRUD
        // ---------------------------
        Route::resource('matches', MatchController::class);


        // ---------------------------
        //         TICKETS CRUD
        // ---------------------------
        Route::resource('tickets', TicketController::class)->only(['index', 'destroy']);


Route::get('/print/persons', [PersonController::class, 'printSelected'])->name('persons.print');
Route::post('/update-assurance', [PersonController::class, 'updateAssurance']);



Route::get('/admin/dashboard_complex/{id}', [App\Http\Controllers\AdminController::class, 'dashboardComplex'])
    ->name('admin.dashboard_complex');


Route::resource('news', NewsController::class)->except(['show']);;
Route::resource('events', EventController::class)->except(['show']);;
    


Route::resource('persons', PersonsController::class);
Route::resource('activity-categories',ActivityCategoryController::class );

Route::resource('seasons', SeasonController::class);


 Route::resource('reservations', \App\Http\Controllers\ReservationController::class);

// جدول المواعيد المحجوزة
Route::get('/admin/schedules/occupied', 
    [\App\Http\Controllers\Admin\ScheduleController::class, 'occupiedSlots']
)->name('admin.schedules.occupied');
// routes/web.php
Route::get('/admin/schedules/occupied-slots', [\App\Http\Controllers\Admin\ScheduleController::class, 'occupiedSlots']);

    
    Route::get('/admin/profile/edit', [RegisterController::class, 'edit'] )->name('admin.profile.edit');

    Route::put('/admin/profile/update', [RegisterController::class, 'update'])->name('admin.profile.update');
    Route::get('admins', [AdminController::class, 'adminsIndex'])->name('admins.index');
    Route::get('admins/create', [AdminController::class, 'adminsCreate'])->name('admins.create');
    Route::post('admins/store', [AdminController::class, 'adminsStore'])->name('admins.store');
    Route::get('admins/edit/{id}', [AdminController::class, 'adminsEdit'])->name('admins.edit');
    Route::post('admins/update/{id}', [AdminController::class, 'adminsUpdate'])->name('admins.update');
    Route::delete('admins/delete/{id}', [AdminController::class, 'adminsDelete'])->name('admins.delete');
   
    Route::get('/admin/dashboard', fn()=>view('admin.dashboard'))->name('admin.dashboard');

    //dossier et validation
    Route::get('/admin/dossiers', [DossierController::class, 'index'])->name('admin.dossiers.index');
    Route::get('/admin/dossiers/{id}/approve', [DossierController::class, 'approve'])->name('admin.dossiers.approve');
    Route::get('/admin/dossiers/{id}/reject', [DossierController::class, 'reject'])->name('admin.dossiers.reject');

// club et validation 
    
    Route::get('/admin/clubs', [ClubController::class, 'index'])
        ->name('admin.clubs.index');

    Route::get('/admin/clubs/{id}/approve', [ClubController::class, 'approve'])
        ->name('admin.clubs.approve');

    Route::get('/admin/clubs/{id}/reject', [ClubController::class, 'reject'])
        ->name('admin.clubs.reject');

Route::post('admin/clubs/{id}/note', [ClubController::class, 'note'])->name('admin.clubs.note');

//activite et complex et pricing pla 
// gestion des activités
    Route::get('/admin/activities', [ActivitysController::class, 'index'])->name('admin.activities.index');
    Route::get('/admin/activities/create', [ActivitysController::class, 'create'])->name('admin.activities.create');
    Route::post('/admin/activities', [ActivitysController::class, 'store'])->name('admin.activities.store');
    Route::get('/admin/activities/{id}/edit', [ActivitysController::class, 'edit'])->name('admin.activities.edit');
    Route::put('/admin/activities/{id}', [ActivitysController::class, 'update'])->name('admin.activities.update');
    Route::delete('/admin/activities/{id}', [ActivitysController::class, 'destroy'])->name('admin.activities.destroy');

// gestion des horaires (schedules)
Route::get('/admin/schedules', [ScheduleController::class, 'index'])->name('admin.schedules.index');
Route::get('/admin/schedules/create', [ScheduleController::class, 'create'])->name('admin.schedules.create');
Route::post('/admin/schedules', [ScheduleController::class, 'store'])->name('admin.schedules.store');
Route::get('/admin/schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('admin.schedules.edit');
Route::put('/admin/schedules/{id}', [ScheduleController::class, 'update'])->name('admin.schedules.update');
Route::delete('/admin/schedules/{id}', [ScheduleController::class, 'destroy'])->name('admin.schedules.destroy');
Route::get('/admin/get-complex-activity', function (Request $request) {
    $ca = \App\Models\ComplexActivity::where('complex_id', $request->complex_id)
        ->where('activity_id', $request->activity_id)
        ->first();

    return response()->json([
        'id' => $ca ? $ca->id : null
    ]);
})->name('admin.getComplexActivity');

// gestion des capacités
// Capacities Management
Route::get('/admin/capacities', [CapacityController::class, 'index'])
    ->name('admin.capacities.index');

Route::get('/admin/capacities/create', [CapacityController::class, 'create'])
    ->name('admin.capacities.create');

Route::post('/admin/capacities', [CapacityController::class, 'store'])
    ->name('admin.capacities.store');

Route::get('/admin/capacities/{id}/edit', [CapacityController::class, 'edit'])
    ->name('admin.capacities.edit');

Route::put('/admin/capacities/{id}', [CapacityController::class, 'update'])
    ->name('admin.capacities.update');

Route::delete('/admin/capacities/{id}', [CapacityController::class, 'destroy'])
    ->name('admin.capacities.destroy');

// gestion des complexes
   Route::get('/admin/complexes', [ComplexeController::class, 'index'])
        ->name('admin.complexes.index');
    Route::get('/admin/complexes/create', [ComplexController::class, 'create'])
        ->name('admin.complexes.create');

    Route::post('/admin/complexes', [ComplexController::class, 'store'])
        ->name('admin.complexes.store');
    // تعديل مركب
    Route::get('/admin/complexes/{id}/edit', [ComplexController::class, 'edit'])
        ->name('admin.complexes.edit');

    Route::put('/admin/complexes/{id}', [ComplexController::class, 'update'])
        ->name('admin.complexes.update');

    // حذف مركب
    Route::delete('/admin/complexes/{id}', [ComplexController::class, 'destroy'])
        ->name('admin.complexes.destroy');

// tableau de bord admin

    Route::get('/admin/pricing', [PricingsPlanController::class, 'index'])->name('admin.pricing_plans.index');
    Route::get('/admin/pricing/create', [PricingsPlanController::class, 'create'])->name('admin.pricing_plans.create');
    Route::post('/admin/pricing', [PricingsPlanController::class, 'store'])->name('admin.pricing_plans.store');
    Route::get('/admin/pricing/{id}/edit', [PricingsPlanController::class, 'edit'])->name('admin.pricing_plans.edit');
    Route::put('/admin/pricing/{id}', [PricingsPlanController::class, 'update'])->name('admin.pricing_plans.update');
    Route::delete('/admin/pricing/{id}', [PricingsPlanController::class, 'destroy'])->name('admin.pricing_plans.destroy');
// mise à jour ملاحظة dossier

Route::post(
    'admin/dossiers/{dossier}/note',
    [DossierController::class, 'updateNote']
)->name('admin.dossiers.note');



Route::resource('/admin/age-categories', AgeCategoryController::class);
 Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->middleware('auth')
    ->name('admin.dashboard');

});




/*
|--------------------------------------------------------------------------
| ESPACE SECURISE (NECESSITE LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

// ChargilyPay Routes

Route::post('chargilypay/redirect', [ChargilyPayController::class, "redirect"])->name("chargilypay.redirect");

Route::get('chargilypay/back', [ChargilyPayController::class, "back"])->name("chargilypay.back");
Route::post('chargilypay/webhook', [ChargilyPayController::class, "webhook"])->name("chargilypay.webhook_endpoint");
// Profile Routes

Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/profile/new', [ProfileController::class, 'newPerson'])
    ->name('profile.new');

Route::get('/profile/step/{step}', [ProfileController::class, 'showStep'])
        ->name('profile.step');
 Route::post('/profile/step/{step}', [ProfileController::class, 'saveStep'])
        ->name('profile.step.save');


 Route::get('/person/{person}/edit/step/{step}', [ProfileController::class, 'editStep'])
    ->name('profile.editStep');

Route::post('/person/{person}/edit/step/{step}', [ProfileController::class, 'saveEditStep'])
    ->name('profile.editStep.save');       



Route::get('/activities', function () {
    return view('activities.index');
})->name('activities');

  
    // Tableau de bord
 Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

    /*
    |--------------------------------------------------------------------------
    | DOSSIERS
    |--------------------------------------------------------------------------
    */
   




Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
Route::post('/activities/store', [ActivityController::class, 'store'])->name('activities.store');

// يجب أن تكون تحت المسارات فوق 👇

Route::get('/activities/register/{id}', [ActivityController::class, 'register'])->name('activities.register');
Route::get('/my-activities', [ActivityController::class, 'myActivities'])->name('my.activities');

// ⚠️ تعديل/حذف تكون دائمًا آخر شيء
Route::get('/activities/{id}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
Route::put('/activities/{id}', [ActivityController::class, 'update'])->name('activities.update');
Route::delete('/activities/{id}', [ActivityController::class, 'destroy'])->name('activities.destroy');





    // تسجيل في نشاط معين

    Route::get('/activities', [App\Http\Controllers\ActivityController::class, 'index'])
        ->name('activities.index');

        Route::get('/activities/{id}/complexes',
    [App\Http\Controllers\ActivityController::class, 'complexes'])
    ->name('activities.complexes');






    // صفحة أنشطتي
  
   Route::get('/complexes', [ComplexeController::class, 'index'])
        ->name('complexes.index');

    Route::post('/complexes', [ComplexeController::class, 'store'])
        ->name('complexes.store');

    Route::put('/complexes/{id}', [ComplexeController::class, 'update'])
        ->name('complexes.update');

    Route::delete('/complexes/{id}', [ComplexeController::class, 'destroy'])
        ->name('complexes.destroy');
    /*
    |--------------------------------------------------------------------------
    | RESERVATIONS
    |--------------------------------------------------------------------------
    */
Route::get('/reservations/{reservation}/print',
    [ReservationController::class, 'print'])
    ->name('reservations.print');


    Route::get('/my-reservations', [ReservationController::class, 'index'])
        ->name('reservation.my-reservations');

    Route::get('/reservations/create', [ReservationController::class, 'create'])
        ->name('reservation.create');

    Route::get('/reservations/{id}/renew', [ReservationController::class, 'renew'])
        ->name('reservation.renew');

        Route::delete('/reservations/{reservation}', 
    [ReservationController::class, 'destroy']
)->name('reservations.destroy');


        Route::post(
    '/reservations/{reservation}/renew',
    [ReservationController::class, 'renewStore']
)->name('reservations.renew.store');







   Route::get('/my-activities', function () {
        return view('activities.my');
    })->name('my.activities');
    // Étape 1 - Choisir type
    Route::get('/reservations/select-type', [ReservationController::class, 'selectType'])
        ->name('reservation.select_type');

    // Étape 2 - Liste des complexes selon type
    Route::get('/reservations/list/{type}', [ReservationController::class, 'listByType'])
        ->name('reservation.list_by_type');

Route::post('/activities/select', function () {
    session(['activity_id' => request('activity_id')]);
    return response()->json(['success' => true]);
})->name('activities.select');



    // Étape 3 - Formulaire
    Route::get('/reservations/form/{id}', [ReservationController::class, 'form'])
        ->name('reservation.form');

    // Étape 4 - Enregistrer
    Route::post('/reservations/store', [ReservationController::class, 'store'])
        ->name('reservation.store');

    // Paiements

   
    Route::get('/payments/{reservation}/pay', [PaymentController::class, 'pay'])
         ->name('payments.pay');


    Route::get('/reservations/payment/{id}', [PaymentController::class, 'paymentPage'])
        ->name('reservation.payment');

    Route::get('/reservations/pay-cash/{id}', [PaymentController::class, 'payCash'])
        ->name('reservation.pay_cash');

    Route::get('/reservations/pay-online/{id}', [PaymentController::class, 'payOnline'])
        ->name('reservation.pay_online');


});

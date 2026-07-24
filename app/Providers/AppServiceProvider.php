<?php

namespace App\Providers;
use Carbon\Carbon;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
     Carbon::setLocale('fr');

// Macro pour un format court français
Carbon::macro('fr', function () {
    return $this->format('d/m/Y');
});

// Macro pour format long
Carbon::macro('frLong', function () {
    return $this->isoFormat('dddd D MMMM YYYY'); // vendredi 19 décembre 2025
});
    // Optionnel : Force le format par défaut pour les dates Carbon
    // Cela affecte ->toDateString() et d'autres méthodes
  
    }
}

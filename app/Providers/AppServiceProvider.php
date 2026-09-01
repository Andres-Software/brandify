<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentTimezone;
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
        // Campos de data/hora do Filament (ex: expires_at) exibem e
        // interpretam no horário local, convertendo para UTC ao salvar —
        // sem isso, o Filament assume UTC e um valor digitado como "19:17"
        // (pensando em horário local) era salvo como 19:17 UTC, expirando
        // 3h antes do esperado.
        FilamentTimezone::set(config('app.timezone'));
    }
}

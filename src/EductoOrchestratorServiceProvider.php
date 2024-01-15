<?php

namespace Jazzlevit\Orchestrator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class EductoOrchestratorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::prefix('orchestrator')
            ->as('orchestrator.')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'orchestrator');
    }
}

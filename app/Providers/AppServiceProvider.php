<?php

namespace App\Providers;

use App\Repositories\Translation\TranslationInterface;
use App\Repositories\Translation\TranslationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TranslationInterface::class, TranslationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

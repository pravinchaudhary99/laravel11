<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Phrases\PhraseInterface;
use App\Repositories\Phrases\PhraseRepository;
use App\Repositories\Translation\TranslationInterface;
use App\Repositories\Translation\TranslationRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TranslationInterface::class, TranslationRepository::class);
        $this->app->bind(PhraseInterface::class, PhraseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

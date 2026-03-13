<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use App\Policies\Poke\PokemonPolicy;
use App\Models\Poke\Pokemon;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Pokemon::class, PokemonPolicy::class);
    }
}

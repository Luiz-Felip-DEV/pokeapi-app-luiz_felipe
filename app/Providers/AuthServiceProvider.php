<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Pokemon;
use App\Policies\PokemonPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Pokemon::class => PokemonPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
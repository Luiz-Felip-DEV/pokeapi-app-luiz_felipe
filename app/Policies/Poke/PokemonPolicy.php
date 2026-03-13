<?php

namespace App\Policies\Poke;

use App\Models\User;

class PokemonPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function import(User $user)
    {
        return true;
    }
}

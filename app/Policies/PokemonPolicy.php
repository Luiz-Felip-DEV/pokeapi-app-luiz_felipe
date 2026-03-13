<?php

namespace App\Policies;

use App\Models\User;

class PokemonPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function view(User $user): bool
    {
        return true;
    }

    public function import(User $user): bool
    {
        return $user->isEditor() || $user->isAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }
}

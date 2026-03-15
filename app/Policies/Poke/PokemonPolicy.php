<?php

namespace App\Policies\Poke;

use App\Models\User;

class PokemonPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function import(User $user): bool
    {
        return in_array($user->role, ['editor', 'admin']);
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    public function favorite(User $user): bool
    {
        return in_array($user->role, ['editor', 'admin']);
    }

    public function users(User $user): bool
    {
        return $user->isAdmin();
    }
}

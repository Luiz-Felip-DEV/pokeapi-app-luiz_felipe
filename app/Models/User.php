<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Factories\UserFactory;
use App\Models\Poke\Pokemon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function isViewer(): bool
    {
        return $this->role == 'viewer';
    }

    public function isAdmin(): bool
    {
        return $this->role == 'admin';
    }

    public function isEditor(): bool
    {
        return $this->role == 'editor';
    }

    public function favorites()
    {
        return $this->belongsToMany(Pokemon::class, 'favorites', 'user_id', 'pokemon_id')->withTimestamps();
    }
}

<?php

namespace App\Models\Poke;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;
use App\Models\Poke\Type;

class Pokemon extends Model
{
    use HasUuids;

    protected $table = 'pokemons';

    protected $fillable = [
        'api_id',
        'name',
        'height',
        'weight',
        'sprite_url',
    ];

    public function types()
    {
       return $this->belongsToMany(Type::class, 'pokemon_type', 'pokemon_id', 'type_id');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'pokemon_id', 'user_id')->withTimestamps();
    }
}

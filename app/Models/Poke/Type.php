<?php

namespace App\Models\Poke;

use Illuminate\Database\Eloquent\Model;
use App\Models\Poke\Pokemon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Type extends Model
{
    use HasUuids;
    
    protected $fillable = ['name'];

    public function pokemons()
    {
        return $this->belongsToMany(Pokemon::class, 'pokemon_type', 'type_id', 'pokemon_id');
    }
}

<?php

namespace App\Services;
use App\Models\Poke\Pokemon;
use App\Models\Poke\Type;
use Illuminate\Support\Facades\Log;

class PokemonImporter
{
    public function import($pokemonData)
    {
        try {
            $pokemon = Pokemon::updateOrCreate(
                ['api_id' => $pokemonData['id']],
                [
                    'name'   => $pokemonData['name'],
                    'height' => $pokemonData['height'],
                    'weight' => $pokemonData['weight'],
                    'sprite_url' => $pokemonData['sprites']['front_default'] ?? null,
                ]
            );

            if (!$pokemon) {
                return null;
            }

            $typeIds = [];
            foreach ($pokemonData['types'] as $typeSlot) {
                $typeName = $typeSlot['type']['name'];
                $type = Type::firstOrCreate(['name' => $typeName]);
                $typeIds[] = $type->id;
            }
 
            $pokemon->types()->sync($typeIds);
 
            return $pokemon;
        } catch (\Exception $e) {
            Log::error('PokemonImporter error: ', ['pokemon' => $pokemonData['name'] ?? 'Ocorreu um erro ao importar pokemon.', 'error'   => $e->getMessage()]);
            return null;
        }
    }
}

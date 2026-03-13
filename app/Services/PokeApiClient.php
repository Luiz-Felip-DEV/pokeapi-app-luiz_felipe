<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class PokeApiClient
{
    private $baseUrlPoke = 'https://pokeapi.co/api/v2';

    public function getPokemon(int $page = 1, int $limit = 20): ?array
    {
      $offset = ($page - 1) * $limit;

      return Cache::remember("pokemon_page_{$page}_limit_{$limit}", 3600, function () use ($offset, $limit) {
          try {
              $response = Http::timeout(10)->get("{$this->baseUrlPoke}/pokemon", [
                  'offset' => $offset,
                  'limit' => $limit,
              ]);

              $response->throw();

              return $response->json();
          } catch (RequestException $e) {
              Log::error("Erro ao buscar pokemons: " . $e->getMessage());
              return null;
          }
      });
    }

    public function getPokemonByName(string $name): ?array
    {
        return Cache::remember("pokemon_{$name}", 3600, function () use ($name) {
            try {
                $response = Http::get("{$this->baseUrlPoke}/pokemon/{$name}");

                $response->throw();

                return $response->json();
            } catch (RequestException $e) {
                Log::error("Erro ao buscar pokemon '{$name}': " . $e->getMessage());
                return null;
            }
        });
    }
}
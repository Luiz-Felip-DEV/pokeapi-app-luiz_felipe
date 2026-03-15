<?php

namespace App\Repositories\Poke;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\PokeApiClient;
use App\Services\PokemonImporter;
use App\Models\Poke\Pokemon;

class PokemonRepository
{
    public function __construct(private PokeApiClient $pokeApiClient, private PokemonImporter $pokemonImporter)
    {
        $this->pokeApiClient   = $pokeApiClient;
        $this->pokemonImporter = $pokemonImporter;
    }

    public function index(User $user, ?string $name = null, ?string $source = null, int $page = 1)
    {
        try {
            if ($user->isViewer() || $source == 'database') {
                $pokemons = Pokemon::with('types')->orderBy('name');
 
                if ($name) {
                    $pokemons->where('name', 'like', "%{$name}%");
                }
 
                $paginated  = $pokemons->paginate(20, ['*'], 'page', $page);
 
                return array (
                    'error'       => false,
                    'pokemons'    => $paginated->items(),
                    'total'       => $paginated->total(),
                    'currentPage' => $paginated->currentPage(),
                    'totalPages'  => $paginated->lastPage(),
                    'source'      => 'database',
                );
            }
 
            if ($name) {
                $pokemon = $this->pokeApiClient->getPokemonByName($name);
 
                if (!$pokemon) {
                    return array (
                        'error'       => true,
                        'pokemons'    => [],
                        'total'       => 1,
                        'currentPage' => 1,
                        'totalPages'  => 1,
                        'source'      => 'database',
                    );
                }
 
                return array (
                    'error'       => false,
                    'pokemons'    => [
                        [
                            'name' => $pokemon['name'],
                            'url'  => config('app.base_url_poke_api') . "/pokemon/{$pokemon['id']}",
                        ]
                    ],
                    'total'       => 1,
                    'currentPage' => 1,
                    'totalPages'  => 1,
                    'source'      => 'api',
                );
            }
 
            $data = $this->pokeApiClient->getPokemon($page);
 
            if (!$data) {
                return array (
                    'error'       => true,
                    'pokemons'    => [],
                    'total'       => 0,
                    'currentPage' => $page,
                    'totalPages'  => 0,
                    'source'      => 'api',
                );
            }
 
            return array (
                'error'       => false,
                'pokemons'    => $data['results'] ?? [],
                'total'       => $data['count'],
                'currentPage' => $page,
                'totalPages'  => ceil($data['count'] / 20),
                'source'      => 'api',
            );
 
        } catch (\Exception $e) {
            return array (
                'error'       => true,
                'pokemons'    => [],
                'total'       => 0,
                'currentPage' => $page,
                'totalPages'  => 0,
                'source'      => $user->isViewer() ? 'database' : 'api',
            );
        }
    }

    public function show(string $name, ?string $source = null)
    {
        try {
            if ($source === 'database' || $source === 'favorites') {
                $pokemon = Pokemon::with('types')->where('name', $name)->first();

                if (!$pokemon) {
                    return array (
                        'error'   => true,
                        'pokemon' => null,
                        'source'  => $source,
                    );
                }

                return array (
                    'error'   => false,
                    'pokemon' => $pokemon,
                    'source'  => $source,
                );
            }

            $pokemon = $this->pokeApiClient->getPokemonByName($name);

            if (!$pokemon) {
                return array (
                    'error'   => true,
                    'pokemon' => null,
                    'source'  => 'api',
                );
            }

            return array (
                'error'   => false,
                'pokemon' => $pokemon,
                'source'  => 'api',
            );
        } catch (\Exception $e) {
            return array (
                'error'   => true,
                'pokemon' => null,
                'source'  => $user->isViewer() ? 'database' : 'api',
            );
        }
    }

    public function import(string $name)
    {
        try {
            $pokemon = $this->pokeApiClient->getPokemonByName($name);

            if (!$pokemon) {
                return array (
                    'error' => true,
                    'message' => 'Pokémon não encontrado.',
                );
            }

            $this->pokemonImporter->import($pokemon);

            return array (
                'error' => false,
                'message' => 'Pokémon importado com sucesso!',
            );
        } catch (\Exception $e) {
            return array (
                'error' => true,
                'message' => 'Ocorreu um erro ao importar o Pokémon. Tente novamente mais tarde.',
            );
        }
    }

    public function storeFavorite(string $name)
    {
        try {
            $pokemon = Pokemon::where('name', $name)->first();

            if (!$pokemon) {
                return array (
                    'error' => true,
                    'message' => 'Pokémon não encontrado.',
                );
            }

            $user = auth()->user();

            if (!$user) {
                return array (
                    'error' => true,
                    'message' => 'Usuário não autenticado.',
                );
            }

            $user->favorites()->syncWithoutDetaching($pokemon->id);

            return array (
                'error' => false,
                'message' => 'Pokémon adicionado aos favoritos!',
            );
        } catch (\Exception $e) {
            return array (
                'error' => true,
                'message' => 'Ocorreu um erro ao adicionar o Pokémon aos favoritos. Tente novamente mais tarde.'
            );
        }
    }

    public function favorites(int $page = 1)
    {
        try {
            $user = auth()->user();
            $pokemons = $user->favorites()->with('types')->orderBy('name')->paginate(20, ['*'], 'page', $page);

            if ($pokemons->isEmpty()) {
                return array (
                    'error'       => false,
                    'pokemons'    => [],
                    'total'       => 0,
                    'currentPage' => 1,
                    'totalPages'  => 1,
                    'source'      => 'favorites',
                );
            }

            return array (
                'error'       => false,
                'pokemons'    => $pokemons->items(),
                'total'       => $pokemons->total(),
                'currentPage' => $pokemons->currentPage(),
                'totalPages'  => $pokemons->lastPage(),
                'source'      => 'favorites',
            );
        } catch (\Exception $e) {
            return array (
                'error'       => true,
                'pokemons'    => [],
                'total'       => 0,
                'currentPage' => 1,
                'totalPages'  => 1,
                'source'      => 'favorites',
            );
        }
    }

    public function destroyFavorite(string $name)
    {
        try {
            $pokemon = Pokemon::where('name', $name)->first();

            if (!$pokemon) {
                return array (
                    'error' => true,
                    'message' => 'Pokémon não encontrado no banco de dados.',
                );
            }

            $user = auth()->user();

            if (!$user) {
                return array (
                    'error' => true,
                    'message' => 'Usuário não autenticado.',
                );
            }

            $user->favorites()->detach($pokemon->id);

            return array (
                'error' => false,
                'message' => 'Pokémon removido dos favoritos!',
            );
        } catch (\Exception $e) {
            return array (
                'error' => true,
                'message' => 'Ocorreu um erro ao remover o Pokémon dos favoritos. Tente novamente mais tarde.'
            );
        }
    }

    public function destroyImported(string $name)
    {
        try {
            $pokemon = Pokemon::where('name', $name)->first();

            if (!$pokemon) {
                return array (
                    'error' => true,
                    'message' => 'Pokémon não encontrado no banco de dados.',
                );
            }

            $pokemon->delete();

            return array (
                'error' => false,
                'message' => 'Pokémon removido com sucesso!',
            );
        } catch (\Exception $e) {
            return array (
                'error' => true,
                'message' => 'Ocorreu um erro ao remover o Pokémon. Tente novamente mais tarde.'
            );
        }
    }

    public function users()
    {
        try {
            $users = User::whereKeyNot(auth()->id())->orderBy('name')->get();

            if ($users->isEmpty()) {
                return array (
                    'error' => false,
                    'message' => 'Nenhum usuário encontrado.',
                );
            }

            return array (
                'error' => false,
                'users' => $users,
                'message' => 'Usuários carregados com sucesso.',
            );
        } catch (\Exception $e) {
            return array (
                'error' => true,
                'message' => 'Ocorreu um erro ao carregar os usuários. Tente novamente mais tarde.'
            );
        }
    }
}
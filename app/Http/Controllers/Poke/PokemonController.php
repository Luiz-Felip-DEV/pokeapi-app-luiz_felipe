<?php

namespace App\Http\Controllers\Poke;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PokeApiClient;
use App\Services\PokemonImporter;
use App\Models\Poke\Pokemon;
use App\Http\Requests\Poke\SearchPokemonRequest;

class PokemonController extends Controller
{
    public function __construct(PokeApiClient $pokeApiClient, PokemonImporter $pokemonImporter)
    {
        $this->pokeApiClient   = $pokeApiClient;
        $this->pokemonImporter = $pokemonImporter;
    }

    public function index(SearchPokemonRequest $request)
    {
        $user   = auth()->user();
        $page   = $request->input('page', 1);
        $name   = $request->input('name');
        $source = $request->input('source') ?? null;
 
        try {
            if ($user->isViewer() || $source == 'database') {
                $pokemons = Pokemon::with('types')->orderBy('name');
 
                if ($name) {
                    $pokemons->where('name', 'like', "%{$name}%");
                }
 
                $paginated  = $pokemons->paginate(20, ['*'], 'page', $page);
 
                return view('pokemon.index', [
                    'pokemons'    => $paginated->items(),
                    'total'       => $paginated->total(),
                    'currentPage' => $paginated->currentPage(),
                    'totalPages'  => $paginated->lastPage(),
                    'source'      => 'database',
                ]);
            }
 
            if ($name) {
                $pokemon = $this->pokeApiClient->getPokemonByName($name);
 
                if (!$pokemon) {
                    return redirect()->route('pokemon.index')->with('error', 'Pokémon não encontrado.');
                }
 
                return view('pokemon.show', ['pokemon' => $pokemon, 'source' => 'api']);
            }
 
            $data = $this->pokeApiClient->getPokemon($page);
 
            if (!$data) {
                return view('pokemon.index', [
                    'pokemons'    => [],
                    'total'       => 0,
                    'currentPage' => $page,
                    'totalPages'  => 0,
                    'source'      => 'api',
                ])->with('error', 'Não foi possível carregar os pokémons. Tente novamente mais tarde.');
            }
 
            return view('pokemon.index', [
                'pokemons'    => $data['results'] ?? [],
                'total'       => $data['count'] ?? 0,
                'currentPage' => $page,
                'totalPages'  => ceil(($data['count'] ?? 0) / 20),
                'source'      => 'api',
            ]);
 
        } catch (\Exception $e) {
            return view('pokemon.index', [
                'pokemons'    => [],
                'total'       => 0,
                'currentPage' => 1,
                'totalPages'  => 0,
                'source'      => $user->isViewer() ? 'database' : 'api',
            ])->with('error', 'Ocorreu um erro ao carregar os pokémons. Tente novamente mais tarde.');
        }
    }

    public function show(string $name, Request $request)
    {
        try {
            $source = $request->input('source') ?? null;

            if ($source === 'database' || $source === 'favorites') {
                $pokemon = Pokemon::with('types')->where('name', $name)->first();

                if (!$pokemon) {
                    return redirect()->route('pokemon.index')->with('error', 'Pokémon não encontrado no banco de dados.');
                }

                return view('pokemon.show', ['pokemon' => $pokemon, 'source' => $source]);
            }

            $pokemon = $this->pokeApiClient->getPokemonByName($name);

            if (!$pokemon) {
            return redirect()->route('pokemon.index')->with('error', 'Pokémon não encontrado.');
            }

            return view('pokemon.show', ['pokemon' => $pokemon, 'source' => 'api']);
        } catch (\Exception $e) {
            return redirect()->route('pokemon.index')->with('error', 'Ocorreu um erro ao carregar o Pokémon. Tente novamente mais tarde.');
        }
    }

    public function import(string $name)
    {
        $this->authorize('import', Pokemon::class);

        try {
            $pokemon = $this->pokeApiClient->getPokemonByName($name);

            if (!$pokemon) {
                return redirect()->route('pokemon.index')->with('error', 'Pokémon não encontrado.');
            }

            $this->pokemonImporter->import($pokemon);

            return redirect()->route('pokemon.index')->with('success', 'Pokémon importado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('pokemon.index')->with('error', 'Ocorreu um erro ao importar o Pokémon. Tente novamente mais tarde.');
        }
    }

    public function storeFavorite(string $name)
    {
        $this->authorize('favorite', Pokemon::class);

        try {
            $pokemon = Pokemon::where('name', $name)->first();

            if (!$pokemon) {
                return redirect()->route('pokemon.index', ['name' => $name])->with('error', 'Pokémon não encontrado no banco de dados. Importe-o antes de favoritar.');
            }

            $user = auth()->user();

            $user->favorites()->syncWithoutDetaching($pokemon->id);

            return redirect()->route('pokemon.index', ['name' => $name])->with('success', 'Pokémon adicionado aos favoritos!');
        } catch (\Exception $e) {
            return redirect()->route('pokemon.index', ['name' => $name])->with('error', 'Ocorreu um erro ao adicionar o Pokémon aos favoritos. Tente novamente mais tarde.');
        }
    }

    public function favorites(Request $request)
    {
        $this->authorize('favorite', Pokemon::class);

        try {
            $page = $request->input('page', 1);
            $user = auth()->user();

            $pokemons = $user->favorites()->with('types')->orderBy('name')->paginate(20, ['*'], 'page', $page);

            if ($pokemons->isEmpty()) {
                return redirect()->route('pokemon.index')->with('info', 'Você ainda não tem pokémons favoritos. Explore e adicione seus favoritos!');
            }

            return view('pokemon.index', [
                'pokemons'    => $pokemons->items(),
                'total'       => $pokemons->total(),
                'currentPage' => $pokemons->currentPage(),
                'totalPages'  => $pokemons->lastPage(),
                'source'      => 'favorites',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('pokemon.index')->with('error', 'Ocorreu um erro ao carregar os favoritos. Tente novamente mais tarde.');
        }
    }

    public function destroyFavorite(string $name)
    {
        $this->authorize('favorite', Pokemon::class);

        try {
            $pokemon = Pokemon::where('name', $name)->first();

            if (!$pokemon) {
                return redirect()->route('pokemon.favorites')->with('error', 'Pokémon não encontrado no banco de dados.');
            }

            $user = auth()->user();

            $user->favorites()->detach($pokemon->id);

            return redirect()->route('pokemon.favorites')->with('success', 'Pokémon removido dos favoritos!');
        } catch (\Exception $e) {
            return redirect()->route('pokemon.favorites')->with('error', 'Ocorreu um erro ao remover o Pokémon dos favoritos. Tente novamente mais tarde.');
        }
    }
}

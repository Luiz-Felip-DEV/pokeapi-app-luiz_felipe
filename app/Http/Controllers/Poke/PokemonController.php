<?php

namespace App\Http\Controllers\Poke;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poke\Pokemon;
use App\Http\Requests\Poke\SearchPokemonRequest;
use App\Http\Requests\Poke\ShowPokemonRequest;
use App\Repositories\Poke\PokemonRepository;

class PokemonController extends Controller
{
    public function __construct(PokemonRepository $pokemonRepository)
    {
        $this->pokemonRepository = $pokemonRepository;
    }

    public function index(SearchPokemonRequest $request)
    {
        $user   = auth()->user();
        $page   = $request->input('page', 1);
        $name   = $request->input('name');
        $source = $request->input('source') ?? null;

        $result = $this->pokemonRepository->index($user, $name, $source, $page);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', 'Ocorreu um erro ao carregar os pokémons. Tente novamente mais tarde.');
        }

        return view('pokemon.index', [
            'pokemons'    => $result['pokemons'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'totalPages'  => $result['totalPages'],
            'source'      => $result['source'],
        ]);
    }

    public function show(string $name, ShowPokemonRequest $request)
    {
        $source = $request->input('source', 'api');

        $result = $this->pokemonRepository->show($name, $source);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', 'Ocorreu um erro ao carregar os detalhes do Pokémon. Tente novamente mais tarde.');
        }

        return view('pokemon.show', [
            'pokemon' => $result['pokemon'],
            'source'  => $result['source'],
        ]);
    }

    public function import(string $name)
    {
        $this->authorize('import', Pokemon::class);

        $result = $this->pokemonRepository->import($name);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return redirect()->route('pokemon.index')->with('success', 'Pokémon importado com sucesso!');
    }

    public function storeFavorite(string $name)
    {
        $this->authorize('favorite', Pokemon::class);

        $result = $this->pokemonRepository->storeFavorite($name);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return redirect()->route('pokemon.index')->with('success', 'Pokémon adicionado aos favoritos!');
    }

    public function favorites(Request $request)
    {
        $this->authorize('favorite', Pokemon::class);

        $result = $this->pokemonRepository->favorites();

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', 'Ocorreu um erro ao carregar os pokémons favoritos. Tente novamente mais tarde.');
        }

        return view('pokemon.index', [
            'pokemons'    => $result['pokemons'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'totalPages'  => $result['totalPages'],
            'source'      => $result['source'],
        ]);
    }

    public function destroyFavorite(string $name)
    {
        $this->authorize('favorite', Pokemon::class);

        $result = $this->pokemonRepository->destroyFavorite($name);

        if ($result['error']) {
            return redirect()->route('pokemon.favorites')->with('error', $result['message']);
        }

        return redirect()->route('pokemon.favorites')->with('success', 'Pokémon removido dos favoritos!');
    }

    public function destroyImported(string $name)
    {
        $this->authorize('delete', Pokemon::class);

        $result = $this->pokemonRepository->destroyImported($name);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return redirect()->route('pokemon.index')->with('success', 'Pokémon removido com sucesso!');
    }
}

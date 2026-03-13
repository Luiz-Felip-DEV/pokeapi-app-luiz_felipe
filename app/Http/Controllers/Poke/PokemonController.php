<?php

namespace App\Http\Controllers\Poke;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PokeApiClient;
use App\Http\Requests\Poke\SearchPokemonRequest;

class PokemonController extends Controller
{
    public function __construct(PokeApiClient $pokeApiClient)
    {
        $this->pokeApiClient = $pokeApiClient;
    }

    public function index(SearchPokemonRequest $request)
    {
        $page = $request->input('page', 1);
        $name = $request->input('name');

        if ($name) {
            $pokemon = $this->pokeApiClient->getPokemonByName($name);
            
            if (!$pokemon) {
                return redirect()->route('pokemon.index')->with('error', 'Pokémon não encontrado.');
            }

            return view('pokemon.show', ['pokemon' => $pokemon]);
        }

        $data = $this->pokeApiClient->getPokemon($page);

        if (!$data) {
            return view('pokemon.index', [
                'pokemons' => [],
                'total' => 0,
                'currentPage' => $page,
                'totalPages' => 0,
            ])->with('error', 'Não foi possível carregar os pokémons. Tente novamente mais tarde.');
        }

        $pokemons   = $data['results'] ?? [];
        $total      = $data['count'] ?? 0;
        $totalPages = ceil($total / 20);

        return view('pokemon.index', [
            'pokemons' => $pokemons,
            'total' => $total,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function show($name)
    {
        $pokemon = $this->pokeApiClient->getPokemonByName($name);

        if (!$pokemon) {
           return redirect()->route('pokemon.index')->with('error', 'Pokémon não encontrado.');
        }

        return view('pokemon.show', ['pokemon' => $pokemon]);
    }
}

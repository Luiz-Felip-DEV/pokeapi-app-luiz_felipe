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

    /**
     * Lista os pokémons, podendo ser filtrados por nome e fonte (API ou banco de dados). Se o usuário for um viewer, apenas os pokémons do banco de dados serão exibidos.
    */
    public function index(SearchPokemonRequest $request)
    {
        $user   = auth()->user();
        $page   = $request->input('page', 1);
        $name   = $request->input('name');
        $source = $request->input('source') ?? null;

        $result = $this->pokemonRepository->index($user, $name, $source, $page);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return view('pokemon.index', [
            'pokemons'    => $result['pokemons'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'totalPages'  => $result['totalPages'],
            'source'      => $result['source'],
        ]);
    }

    /**
     * Exibe os detalhes de um pokémon específico, buscando as informações na API ou no banco de dados, dependendo da fonte especificada
    */
    public function show(string $name, ShowPokemonRequest $request)
    {
        $source = $request->input('source', 'api');

        $result = $this->pokemonRepository->show($name, $source);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return view('pokemon.show', [
            'pokemon' => $result['pokemon'],
            'source'  => $result['source'],
        ]);
    }

    /**
     * Importa um pokémon da API para o banco de dados, somente as roles autorizadas podem fazer isso.
    */
    public function import(string $name)
    {
        $this->authorize('import', Pokemon::class);

        $result = $this->pokemonRepository->import($name);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return redirect()->route('pokemon.index')->with('success', $result['message']);
    }

    /**
     * Adiciona um pokémon aos favoritos do usuário autenticado.
    */
    public function storeFavorite(string $name)
    {
        $this->authorize('favorite', Pokemon::class);

        $result = $this->pokemonRepository->storeFavorite($name);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return redirect()->route('pokemon.index')->with('success', $result['message']);
    }

    /**
     * Exibe a lista de pokémons favoritos do usuário autenticado.
    */
    public function favorites(Request $request)
    {
        $this->authorize('favorite', Pokemon::class);

        $result = $this->pokemonRepository->favorites();

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return view('pokemon.index', [
            'pokemons'    => $result['pokemons'],
            'total'       => $result['total'],
            'currentPage' => $result['currentPage'],
            'totalPages'  => $result['totalPages'],
            'source'      => $result['source'],
        ]);
    }

    /**
     * Remove o pokémon dos favoritos do usuário autenticado.
    */
    public function destroyFavorite(string $name)
    {
        $this->authorize('favorite', Pokemon::class);

        $result = $this->pokemonRepository->destroyFavorite($name);

        if ($result['error']) {
            return redirect()->route('pokemon.favorites')->with('error', $result['message']);
        }

        return redirect()->route('pokemon.favorites')->with('success', $result['message']);
    }

    /**
     * Remove um pokémon importado do banco de dados, somente a role de administrador pode fazer isso.
    */
    public function destroyImported(string $name)
    {
        $this->authorize('delete', Pokemon::class);

        $result = $this->pokemonRepository->destroyImported($name);

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return redirect()->route('pokemon.index')->with('success', $result['message']);
    }

    /**
     * Exibe a lista de usuários do sistema, somente a role de administrador pode acessar essa funcionalidade.
    */
    public function users()
    {
        $this->authorize('users', Pokemon::class);

        $result = $this->pokemonRepository->users();

        if ($result['error']) {
            return redirect()->route('pokemon.index')->with('error', $result['message']);
        }

        return view('users.users', [
            'users' => $result['users']
        ]);
    }

    /**
     * Exibe os detalhes de um usuário específico, somente a role de administrador pode acessar essa funcionalidade.
    */
    public function showUser($id)
    {
        $this->authorize('users', Pokemon::class);

        $result = $this->pokemonRepository->showUser($id);

        if ($result['error']) {
            return redirect()->route('users.users')->with('error', $result['message']);
        }

        return view('users.show', [
            'user'    => $result['user'],
            'message' => $result['message']
        ]);
    }

    /**
     * Atualiza a role de um usuário específico, somente a role de administrador pode acessar essa funcionalidade.
    */
    public function updateUserRole(Request $request, $id)
    {
        $this->authorize('users', Pokemon::class);

        $role = $request->input('role');

        $result = $this->pokemonRepository->updateUserRole($id, $role);

        if ($result['error']) {
            return redirect()->route('users.users', $id)->with('error', $result['message']);
        }

        return redirect()->route('users.users', $id)->with('success', $result['message']);
    }
}

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Poke\PokemonController;

Route::get('/', function () {
    return redirect()->route('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('pokemon.index');
    });
    Route::get('/pokemons', [PokemonController::class, 'index'])->name('pokemon.index');
    Route::get('/pokemons/favorites', [PokemonController::class, 'favorites'])->name('pokemon.favorites');
    Route::get('/pokemons/{name}', [PokemonController::class, 'show'])->name('pokemon.show');
    Route::post('/pokemons/{name}/import', [PokemonController::class, 'import'])->name('pokemon.import');
    Route::post('/pokemons/{name}/favorite', [PokemonController::class, 'storeFavorite'])->name('pokemon.storeFavorite');
    Route::delete('/pokemons/{name}/favorite', [PokemonController::class, 'destroyFavorite'])->name('pokemon.destroyFavorite');
    Route::delete('/pokemons/{name}/imported', [PokemonController::class, 'destroyImported'])->name('pokemon.destroyImported');
    Route::get('/pokemon/users', [PokemonController::class, 'users'])->name('pokemon.users');
});

require __DIR__.'/auth.php';

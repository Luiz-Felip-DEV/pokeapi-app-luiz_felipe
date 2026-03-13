<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Poke\PokemonController;

Route::get('/', function () {
    return redirect()->route('pokemon.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/pokemons', [PokemonController::class, 'index'])->name('pokemon.index');
    Route::get('/pokemons/{name}', [PokemonController::class, 'show'])->name('pokemon.show');
});

require __DIR__.'/auth.php';

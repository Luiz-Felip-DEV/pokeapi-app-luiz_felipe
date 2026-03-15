<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl capitalize">{{ $pokemon['name'] }}</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6 px-4">

        <a href="{{ route('pokemon.index') }}" class="text-blue-600 text-sm">Voltar</a>

        <div class="bg-white rounded shadow p-6 mt-4 text-center">
            @if ($source === 'database' || $source === 'favorites')
                <img src="{{ $pokemon->sprite_url ?? '' }}" alt="{{ $pokemon->name }}" class="mx-auto w-32 h-32">
            @else
            <img src="{{ $pokemon['sprites']['front_default'] }}"
                 alt="{{ $pokemon['name'] }}" class="mx-auto w-32 h-32">
            @endif

            <h1 class="text-2xl font-bold capitalize mt-2">{{ $pokemon['name'] }}</h1>

            <div class="mt-4 text-gray-600 text-sm">
                <p>Altura: {{ $pokemon['height'] / 10 }} m</p>
                <p>Peso: {{ $pokemon['weight'] / 10 }} kg</p>
            </div>

            <div class="mt-4 flex justify-center gap-2">
                @if ($source === 'database' || $source === 'favorites')
                    @foreach ($pokemon->types as $type)
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm capitalize">
                            {{ $type->name }}
                        </span>
                    @endforeach
                @else
                    @foreach ($pokemon['types'] as $type)
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm capitalize">
                            {{ $type['type']['name'] }}
                        </span>
                    @endforeach
                @endif
            </div>

            @if ($source !== 'database' && $source !== 'favorites')
                @can('import', App\Models\Poke\Pokemon::class)
                    <form method="POST" action="{{ route('pokemon.import', $pokemon['name']) }}" class="mt-6">
                        @csrf
                        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Importar para o banco
                        </button>
                    </form>
                @endcan
            @else
                @if ($source !== 'favorites')
                    @can('favorite', App\Models\Poke\Pokemon::class)
                    <form method="POST" action="{{ route('pokemon.storeFavorite', $pokemon['name']) }}" class="mt-6">
                        @csrf
                        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            Adicionar aos favoritos
                        </button>
                    </form>

                    @can('delete', App\Models\Poke\Pokemon::class)
                        <form method="POST" action="{{ route('pokemon.destroyImported', $pokemon['name']) }}" class="mt-6">
                            @method('DELETE')
                            @csrf
                            <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                                Excluir importado
                            </button>
                        </form>
                    @endcan
                @endcan
                @else
                    @can('favorite', App\Models\Poke\Pokemon::class)
                    <form method="POST" action="{{ route('pokemon.destroyFavorite', $pokemon['name']) }}" class="mt-6">
                        @method('DELETE')
                        @csrf
                        <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                            Remover dos favoritos
                        </button>
                    </form>
                @endif
                @endcan
            @endif
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Pokédex</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4">

        @if (session('error'))
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('pokemon.index') }}" class="mb-6 flex gap-2">
            <input type="text" name="name" value="{{ request('name') }}"
                placeholder="Buscar por nome..."
                class="border rounded px-3 py-2 w-full" />
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Buscar</button>
        </form>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @foreach ($pokemons as $pokemon)
                @php $name = $pokemon['name'];
                     $id = basename($pokemon['url']); @endphp
                <a href="{{ route('pokemon.show', $name) }}"
                   class="bg-white rounded shadow p-4 text-center hover:shadow-md transition">
                    <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/{{ $id }}.png"
                         alt="{{ $name }}" class="mx-auto w-20 h-20">
                    <p class="mt-2 capitalize font-medium">{{ $name }}</p>
                </a>
            @endforeach
        </div>

        <div class="flex justify-center gap-2 mt-8">
            @if ($currentPage > 1)
                <a href="{{ route('pokemon.index', ['page' => $currentPage - 1]) }}"
                class="bg-blue-600 text-white px-4 py-2 rounded">Anterior</a>
            @endif

            <span class="px-4 py-2">{{ $currentPage }} / {{ $totalPages }}</span>

            @if ($currentPage < $totalPages)
                <a href="{{ route('pokemon.index', ['page' => $currentPage + 1]) }}"
                class="bg-blue-600 text-white px-4 py-2 rounded">Próxima</a>
            @endif
        </div>
    </div>
</x-app-layout>
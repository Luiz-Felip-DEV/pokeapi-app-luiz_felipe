<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Usuários</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4">

        @if (session('error'))
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse ($users as $user)
                <div class="bg-white rounded shadow p-4 hover:shadow-md transition">
                    
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>

                        <div>
                            <p class="font-medium">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs px-2 py-1 rounded bg-gray-200 capitalize">
                            {{ $user->role }}
                        </span>

                        <a href="{{ route('users.show', $user->id) }}"
                           class="text-blue-600 text-sm hover:underline">
                            Ver
                        </a>
                    </div>

                </div>
            @empty
                <p class="col-span-4 text-center text-gray-500 py-10">
                    Nenhum usuário encontrado.
                </p>
            @endforelse
        </div>

    </div>
</x-app-layout>
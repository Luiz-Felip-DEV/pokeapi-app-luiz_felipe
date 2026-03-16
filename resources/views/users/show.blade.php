<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Detalhes do Usuário</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4">

        @if (session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded shadow p-6">

            <div class="mb-6">
                <p class="text-lg font-semibold">{{ $user->name }}</p>
                <p class="text-gray-600">{{ $user->email }}</p>
                <p class="text-sm text-gray-500 mt-1">
                    Criado em {{ $user->created_at->format('d/m/Y H:i') }}
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
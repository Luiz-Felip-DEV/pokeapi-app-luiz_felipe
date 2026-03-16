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

            <form method="POST" action="{{ route('users.updateRole', $user->id) }}">
                @csrf
                @method('PUT')

                <label class="block text-sm font-medium mb-2">
                    Alterar Role
                </label>

                <select name="role"
                        class="border rounded px-3 py-2 w-full mb-4">
                    <option value="viewer" {{ $user->role === 'viewer' ? 'selected' : '' }}>
                        Viewer
                    </option>
                    <option value="editor" {{ $user->role === 'editor' ? 'selected' : '' }}>
                        Editor
                    </option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                        Admin
                    </option>
                </select>

                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Atualizar Role
                </button>
            </form>

        </div>
    </div>
</x-app-layout>
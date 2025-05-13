<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Todo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md dark:bg-green-900 dark:text-green-200" role="alert">
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between items-center">
                    <span class="text-lg font-semibold">Daftar Todo</span>
                    <a href="{{ route('todo.create') }}" class="text-xs text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg px-3 py-2">
                        + Tambah Todo
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Judul</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($todos as $data)
                                <tr class="odd:bg-white even:bg-gray-50 border-b dark:border-gray-700 odd:dark:bg-gray-900 even:dark:bg-gray-800">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('todo.edit', $data) }}" class="hover:underline text-xs">
                                            {{ $data->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($data->category)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded dark:bg-blue-900 dark:text-blue-300">
                                                {{ $data->category->name }}
                                            </span>
                                        @else
                                            <span class="inline-block bg-gray-100 text-gray-800 text-xs font-medium px-2 py-1 rounded dark:bg-gray-900 dark:text-gray-300">
                                                Tanpa Kategori
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if (!$data->is_complete)
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded dark:bg-red-900 dark:text-red-300">
                                                On Going
                                            </span>
                                        @else
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded dark:bg-green-900 dark:text-green-300">
                                                Done
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-3">
                                            @if (!$data->is_complete)
                                                <form action="{{ route('todo.complete', $data) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-xs text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg px-2.5 py-1.5 dark:bg-green-600 dark:hover:bg-green-700">
                                                        Selesai
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('todo.uncomplete', $data) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-xs text-white bg-yellow-600 hover:bg-yellow-700 font-medium rounded-lg px-2.5 py-1.5 dark:bg-yellow-500 dark:hover:bg-yellow-600">
                                                        Batalkan
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('todo.destroy', $data) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus todo ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:underline">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="odd:bg-white even:bg-gray-50 border-b dark:border-gray-700 odd:dark:bg-gray-900 even:dark:bg-gray-800">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data todo
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($todosCompleted > 1)
                    <div class="px-6 py-4">
                        <form action="{{ route('todo.deleteallcompleted') }}" method="POST" onsubmit="return confirm('Hapus semua todo yang selesai?')">
                            @csrf
                            @method('DELETE')
                            <x-primary-button>
                                Hapus Semua Todo Selesai
                            </x-primary-button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

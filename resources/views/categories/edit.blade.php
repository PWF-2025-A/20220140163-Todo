<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Kategori') }}
        </h2>
    </x-slot>

    {{-- Header Info Box --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __('Edit Kategori Page') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <div class="p-6 text-gray-900 dark:text-gray-100">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')

            {{-- Nama Kategori --}}
            <div class="mb-6">
                <x-input-label for="name" :value="__('Nama Kategori')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                    :value="old('name', $category->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            {{-- Deskripsi --}}
            <div class="mb-6">
                <x-input-label for="description" :value="__('Deskripsi')" />
                <textarea id="description" name="description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $category->description) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('description')" />
            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
                <x-cancel-button href="{{ route('categories.index') }}" />
            </div>
        </form>
    </div>
</x-app-layout>

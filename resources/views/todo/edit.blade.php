<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Todo') }}
        </h2>

    </x-slot>


<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{ __('Edit Todo Page') }}
            </div>
        </div>

    </div>
</div>

<div class="p-6 text-gray-900 dark:text-gray-100">
    <form method="post" action="{{ route('todo.update', $todo) }}" class="">
        @csrf
        @method('patch')

        <div class="mb-6">
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                :value="old('name', $todo->title)" required autofocus autocomplete="title" />
            <x-input-error class="mt-2" :messages="$errors->get('title')" />
        </div>

        <div class="mb-6">
            <x-input-label for="description" :value="__('Description')" />
            <textarea id="description" name="description" rows="3" class="mt-1 block w-full" required>{{ old('description', $todo->description) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div class="mb-6">
            <x-input-label for="category_id" :value="__('Category')" />
            <select id="category_id" name="category_id" class="mt-1 block w-full" required>
                <option value="">{{ __('Select Category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $todo->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            <x-cancel-button href="{{ route('todo.index') }}" />
        </div>
    </form>
</div>


</x-app-layout>
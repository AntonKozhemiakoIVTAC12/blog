<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('DOCUMENTAL') }}

        </h2>
        <a href="{{ route('articles.index') }}" class="btn btn-primary mt-4">Список документов</a>
    </x-slot>
</x-app-layout>

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h1>{{ $article->title }}</h1>
            <div>
                <a href="{{ route('articles.edit', $article) }}" class="btn btn-warning">Редактировать</a>
                <a href="{{ route('articles.pdf', $article) }}" class="btn btn-primary">Экспорт в PDF</a>
            </div>
        </div>

        @if(isset($article->gost_data) && is_array($article->gost_data))
            @foreach($article->gost_data as $item)
                @php
                    // Проверяем, есть ли у элемента ключ и контент
                    $key = $item['key'] ?? 'Без названия';
                    $content = $item['content'] ?? '';
                    $uniqueId = 'editor-' . md5($key . $loop->index);
                @endphp

                <div class="dynamic-field" data-field-key="{{ $key }}">
                    <i class="fas fa-times remove-component"></i>
                    <label class="form-label fw-bold mb-3">{{ $key }}</label>
                    <textarea id="{{ $uniqueId }}"
                              class="form-control tinymce-editor"
                              name="gost_data[{{ $key }}][]"
                              rows="4"
                              required>{{ old("gost_data.$key.$loop->index", $content) }}</textarea>
                </div>
            @endforeach
        @endif

        <div class="mt-4">
            <p>Автор: {{ $article->user->name ?? 'Неизвестен' }}</p>
            <p>Стандарт: {{ $standards[$article->standard] ?? 'Неизвестен' }}</p>
            <p>Создано: {{ $article->created_at->format('d.m.Y H:i') }}</p>
            <a href="{{ route('articles.index') }}" class="btn btn-secondary">Назад к списку</a>
        </div>
    </div>
@endsection

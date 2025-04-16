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

        @if(isset($article->gost_data))
            @foreach($article->gost_data as $field => $value)
                @if(!empty($value))
                    <div class="card mb-3">
                        <div class="card-header">
                            {{ $gostFields[$field] ?? $field }}
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($value)) !!}
                        </div>
                    </div>
                @endif
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

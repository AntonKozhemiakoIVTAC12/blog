@extends('layouts.app')

@section('content')
    <div class="container">
        <link rel="stylesheet" href="https://bootstraptema.ru/snippets/style/2015/bootswatch/bootstrap-darkly-v3.3.6.css" media="screen">

        <style>
            /* Добавим стили для центрирования кнопок и уменьшения размера */
            .btn-centered {
                display: flex;
                justify-content: center;
                align-items: center;
                transform: scale(1); /* Уменьшим размер в два раза */
            }

            /* Изменим цвет текста на черный */
            .text-black {
                color: black;
            }

            /* Изменим цвет текста внутри кнопок */
            .btn-black-text {
                color: white;
            }
        </style>
        <div class="search">
            <form action="{{ route('articles.search') }}" method="post">
                @csrf
                <input style="color: black;" type="text" name="query" placeholder="Поиск...">
                <button style="color: black;" type="submit">Искать</button>
            </form>
        </div>
        <h2 class="text-black">Список статей </h2>

        <a href="{{ route('articles.create') }}" class="btn btn-primary mt-4 btn-centered btn-black-text">Создать статью</a>

        @forelse ($articles as $article)
            <div class="card mb-3">
                <div class="card-body">
                    <h3 class="card-title text-black">{{ $article->title }}</h3>
                    <p class="card-text text-black">
                        Автор: {{ $article->user->name }} |
                        Дата создания: {{ $article->created_at->format('d.m.Y H:i') }}
                    </p>
                    <a href="{{ route('articles.show', $article->id) }}" class="btn btn-primary btn-centered btn-black-text">Подробнее</a>
                    <a href="{{ route('articles.edit', $article->id) }}" class="btn btn-primary mt-4 btn-centered btn-black-text">Изменить статью</a>
                </div>
            </div>
        @empty
            <p class="text-black">Нет доступных статей.</p>
        @endforelse
    </div>
@endsection

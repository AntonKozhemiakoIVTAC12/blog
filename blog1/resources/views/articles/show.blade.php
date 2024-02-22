@extends('layouts.app')

@section('content')
    <div class="container">
        <link rel="stylesheet" href="https://bootstraptema.ru/snippets/style/2015/bootswatch/bootstrap-darkly-v3.3.6.css" media="screen">

        <h1 style="color: black;">{{ $article->title }}</h1>
        <p style="color: black;">{{ $article->content }}</p>
        <p style="color: black;">{{ $article->categories }}</p>
        @if ($article->user)
            <p style="color: black;">Автор: {{ $article->user->name }}</p>
        @else
            <p style="color: black;">Автор неизвестен</p>
        @endif

        @if ($article->created_at)
            <p style="color: black;">Дата создания: {{ $article->created_at->format('d.m.Y H:i') }}</p>
        @else
            <p style="color: black;">Дата создания неизвестна</p>
        @endif

        <a href="{{ route('articles.index') }}" class="btn btn-secondary" style="color: black;">Назад к списку</a>
    </div>
@endsection

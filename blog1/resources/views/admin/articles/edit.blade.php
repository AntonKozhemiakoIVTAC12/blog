@extends('layouts.admin_layout')

@section('content')
    <div class="container">
        <link rel="stylesheet" href="https://bootstraptema.ru/snippets/style/2015/bootswatch/bootstrap-darkly-v3.3.6.css" media="screen">
        <h1 style="color: black;">Редактировать статью</h1>
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-check"></i>{{ session('success') }}</h4>
            </div>
        @endif
        <form action="{{ route('admin.articles.update', $article) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="title" class="form-label" style="color: black;">Заголовок</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ $article->title }}" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label" style="color: black;">Содержание</label>
                <!-- Используйте стили для растягивания текстового поля ввода -->
                <textarea class="form-control" id="content" name="content" style="height: 300px; resize: vertical;" required>{{ $article->content }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="color: white;">Обновить статью</button>
            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary" style="color: white;">Назад к списку</a>
        </form>
    </div>
@endsection

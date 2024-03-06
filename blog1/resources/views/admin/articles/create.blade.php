@extends('layouts.admin_layout')
@section('content')
    <div class="container">
        <link rel="stylesheet" href="https://bootstraptema.ru/snippets/style/2015/bootswatch/bootstrap-darkly-v3.3.6.css" media="screen">
        <h1 style="color: black;">Создать статью</h1>

        <form action="{{ route('admin.articles.store') }}" method="post">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label" style="color: black;">Заголовок</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label" style="color: black;">Содержание</label>
                <textarea class="form-control" id="content" name="content" style="height: 300px; resize: vertical;" required></textarea>
            </div>
            <!-- Добавим скрытое поле для передачи user_id -->
            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
            <div class="form-group">
                <!-- select -->
                <div style="color: black;" class="form-group">
                    <label>Выберите категорию</label>
                    <select name="cat_id" class="form-control" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['title'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="color: white;">Создать статью</button>
            <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary" style="color: white;">Назад к списку</a>
        </form>
    </div>
@endsection

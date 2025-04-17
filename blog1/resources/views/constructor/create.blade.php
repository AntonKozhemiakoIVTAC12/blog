
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Создать новый компонент</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('components.store') }}" method="POST">
            @csrf

            <!-- Standard Key -->
            <div class="mb-3">
                <label for="standard_key" class="form-label">Стандарт</label>
                <select name="standard_key" id="standard_key" class="form-control" required>
                    <option value="gost34">ГОСТ 34</option>
                    <option value="gost19">ГОСТ 19</option>
                    <option value="ieee830">IEEE STD 830-1998</option>
                    <option value="iso29148">ISO/IEC/IEEE 29148-2011</option>
                </select>
            </div>

            <!-- Key -->
            <div class="mb-3">
                <label for="key" class="form-label">Ключ (уникальный идентификатор)</label>
                <input type="text" name="key" id="key" class="form-control">
                <small class="text-muted">Если не заполнено, ключ будет сгенерирован автоматически.</small>
            </div>

            <!-- Label -->
            <div class="mb-3">
                <label for="label" class="form-label">Название компонента</label>
                <input type="text" name="label" id="label" class="form-control" required>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>

            <!-- Order -->
            <div class="mb-3">
                <label for="order" class="form-label">Порядок сортировки</label>
                <input type="number" name="order" id="order" class="form-control" min="1">
            </div>

            <!-- Submit Button -->
            <a href="{{ route('articles.create') }}" class="btn btn-secondary btn-lg me-3">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <button type="submit" class="btn btn-primary btn-lg">Создать компонент</button>
        </form>
    </div>
@endsection

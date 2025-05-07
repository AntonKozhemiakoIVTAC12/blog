@extends('layouts.admin_layout')

@section('content')
    <div class="container py-4">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <style>
            .doc-card {
                background: #f8f9fa;
                border: none;
                border-radius: 15px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                transition: transform 0.2s;
            }

            .doc-card:hover {
                transform: translateY(-5px);
            }

            .search-form {
                max-width: 500px;
                margin: 2rem auto;
            }

            .search-input {
                border-radius: 25px;
                padding: 12px 20px;
                border: 2px solid #ced4da;
            }

            .action-btns .btn {
                margin: 0 5px;
                min-width: 120px;
            }

            .doc-meta {
                color: #6c757d;
                font-size: 0.9em;
                border-bottom: 1px solid #eee;
                padding-bottom: 0.5rem;
                margin-bottom: 1rem;
            }

            .pagination-wrapper {
                margin-top: 2rem;
                display: flex;
                justify-content: center;
            }
        </style>

        <div class="search-form">
            <form action="{{ route('admin.articles.index') }}" method="GET" class="input-group">
                <input type="text"
                       name="query"
                       class="form-control search-input"
                       placeholder="Поиск в документации..."
                       value="{{ request('query') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Поиск
                </button>
            </form>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 fw-bold text-primary">
                <i class="fas fa-book me-2"></i>Техническая документация
            </h2>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-success btn-lg">
                <i class="fas fa-plus-circle me-2"></i>Создать документ
            </a>
        </div>

        @if(request('query'))
            <div class="alert alert-info">
                Результаты поиска для: <strong>{{ request('query') }}</strong>
            </div>
        @endif

        @forelse ($articles as $article)
            <div class="doc-card mb-4">
                <div class="card-body">
                    <h3 class="h4 card-title fw-bold mb-3">
                        <i class="fas fa-file-alt text-secondary me-2"></i>{{ $article->title }}
                    </h3>

                    <div class="doc-meta">
                        <span class="me-3">
                            <i class="fas fa-user-tie me-1"></i>{{ $article->user->name }}
                        </span>
                        <span>
                            <i class="fas fa-clock me-1"></i>
                            {{ $article->created_at->format('d.m.Y H:i') }}
                        </span>
                    </div>

                    <div class="action-btns d-flex align-items-center">
                        <a href="{{ route('admin.articles.show', $article->id) }}"
                           class="btn btn-primary">
                            <i class="fas fa-eye me-2"></i>Просмотр
                        </a>

                        <a href="{{ route('admin.articles.edit', $article->id) }}"
                           class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Редактировать
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i>
                На данный момент в системе нет технической документации.
            </div>
        @endforelse

        <div class="pagination-wrapper">
            {{ $articles->links() }}
        </div>
    </div>

    <!-- Подключение необходимых скриптов -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @endsection

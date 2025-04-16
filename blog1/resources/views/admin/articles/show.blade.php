@extends('layouts.admin_layout')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-4 align-items-start">
            <div>
                <h1>{{ $article->title }}</h1>
                <div class="badge bg-secondary">
                    Стандарт: {{ $standards[$article->standard] ?? 'Неизвестен' }}
                </div>
            </div>
            <div class="btn-group">
                <a href="{{ route('articles.edit', $article) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Редактировать
                </a>
                <a href="{{ route('articles.pdf', $article) }}" class="btn btn-primary">
                    <i class="fas fa-file-pdf me-2"></i>PDF
                </a>
            </div>
        </div>

        @if($filteredGostData)
            <div class="accordion" id="documentSections">
                @foreach($filteredGostData as $key => $value)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $loop->index }}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $loop->index }}">
                                {{ $gostFields[$key] ?? $key }}
                            </button>
                        </h2>
                        <div id="collapse{{ $loop->index }}"
                             class="accordion-collapse collapse"
                             data-bs-parent="#documentSections">
                            <div class="accordion-body">
                                <div class="document-content">
                                    {!! nl2br(e($value)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-warning">
                Документ не содержит данных
            </div>
        @endif

        <div class="mt-4 card-footer">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1">
                        <i class="fas fa-user me-2"></i>
                        Автор: {{ $article->user->name ?? 'Неизвестен' }}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        Создано: {{ $article->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('articles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Назад
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

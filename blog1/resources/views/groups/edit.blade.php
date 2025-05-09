@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Редактирование группы
                </h2>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('groups.update', $group) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="form-label fw-bold">
                            <i class="fas fa-users me-2"></i>Название группы
                        </label>
                        <input type="text"
                               class="form-control form-control-lg @error('name') is-invalid @enderror"
                               id="name"
                               name="name"
                               value="{{ old('name', $group->name) }}"
                               required>

                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-bold">
                            <i class="fas fa-align-left me-2"></i>Описание группы
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description"
                                  name="description"
                                  rows="4">{{ old('description', $group->description) }}</textarea>

                        @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Сохранить изменения
                        </button>

                        <a href="{{ route('groups.show', $group) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .card-header h2 {
            font-size: 1.8rem;
            font-weight: 500;
        }

        .form-label {
            font-size: 1.1rem;
        }

        .invalid-feedback {
            font-size: 0.9rem;
        }
    </style>
@endsection

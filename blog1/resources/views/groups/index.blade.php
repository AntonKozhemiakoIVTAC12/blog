@extends('layouts.app')

@section('content')
    <style>
        .group-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: transform 0.2s;
            position: relative;
        }

        .group-card:hover {
            transform: translateY(-3px);
        }

        .group-actions {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .admin-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 0.8rem;
        }
    </style>

    <div class="navigation-header">
        <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> К документам
        </a>

        <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <h1 class="h3 mb-0">Список групп</h1>
            <a href="{{ route('groups.create') }}" class="btn btn-success btn-lg">
                <i class="fas fa-plus-circle me-2"></i>Создать группу
            </a>
        </div>
    </div>
    @foreach($groups as $group)
        <div class="group-card">
            @if(auth()->user()->id === $group->admin_id)
                <span class="badge bg-warning admin-badge">
                    <i class="fas fa-crown"></i> Вы администратор
                </span>
            @endif

            <h3>{{ $group->name }}</h3>
            <p class="text-muted">
                <i class="fas fa-users"></i> Участников: {{ $group->users->count() }}
            </p>

            <div class="group-actions">
                @can('update', $group)
                    <a href="{{ route('groups.edit', $group) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Редактировать
                    </a>
                @endcan

                @unless($group->users->contains(auth()->id()))
                    <form action="{{ route('groups.join', $group) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Вступить
                        </button>
                    </form>
                @endunless
            </div>
        </div>
        <div class="mt-4">
                {{ $groups->links('pagination::bootstrap-4') }}
        </div>
    @endforeach
@endsection

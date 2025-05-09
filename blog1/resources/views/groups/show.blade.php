@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    <i class="fas fa-users me-2"></i>{{ $group->name }}
                    @can('update', $group)
                        <a href="{{ route('groups.edit', $group) }}" class="btn btn-warning float-end">
                            <i class="fas fa-edit"></i> Редактировать
                        </a>
                    @endcan
                </h2>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="mb-3">Описание:</h4>
                        <p class="lead">{{ $group->description ?? 'Описание отсутствует' }}</p>

                        <h4 class="mb-3">Участники ({{ $group->users->count() }}):</h4>
                        <ul class="list-group">
                            @foreach($group->users as $user)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $user->name }}
                                    @if($user->id === $group->admin_id)
                                        <span class="badge bg-primary">Администратор</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-primary">
                            <div class="card-header bg-light">
                                Управление группой
                            </div>
                            <div class="card-body">
                                @can('delete', $group)
                                    <form action="{{ route('groups.destroy', $group) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-danger btn-block mb-2"
                                                onclick="return confirm('Вы уверены? Группа будет удалена безвозвратно!')">
                                            <i class="fas fa-trash-alt me-2"></i>Удалить группу
                                        </button>
                                    </form>
                                @endcan

                                <a href="{{ route('groups.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left me-2"></i>Назад к списку
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .list-group-item {
            transition: transform 0.2s;
        }

        .list-group-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
    </style>
@endsection

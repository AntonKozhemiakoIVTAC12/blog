@extends('layouts.admin_layout')
@section('content')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Редактировать пользователя</h3>
        </div>
        <div class="box-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
                </div>

                <div class="form-group">
                    <label for="roles">Роли</label>
                    <select name="roles[]" id="roles" class="form-control" multiple>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            </form>
        </div>
    </div>
@stop

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        // Получите список всех пользователей
        $users = User::all();
        $users_count = User::all()->count();

        return view('admin.users.index', compact('users', 'users_count'));
    }

    public function edit(User $user)
    {
        // Получите список ролей для отображения в форме редактирования
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Обновление данных пользователя
        $user->update($request->all());

        // Обновление ролей пользователя
        $user->syncRoles($request->input('roles', []));

        return redirect()->route('users.index')
            ->with('success', 'Пользователь успешно обновлен');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Пользователь успешно удален');
    }
}

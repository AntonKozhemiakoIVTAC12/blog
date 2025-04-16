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
        $users = User::all();
        $users_count = User::all()->count();

        return view('admin.users.index', compact('users', 'users_count'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->all());

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

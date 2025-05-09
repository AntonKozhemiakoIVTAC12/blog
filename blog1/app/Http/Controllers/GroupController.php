<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('groups.index', compact('groups'));
    }

    public function join(Group $group)
    {
        $group->users()->attach(auth()->id());
        return redirect()->route('groups.index')->with('success', 'Вы успешно вступили в группу');
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'admin_id' => auth()->id(),
        ]);

        $group->users()->attach(auth()->user());

        return redirect()->route('groups.show', $group);
    }

    public function show(Group $group)
    {
        return view('groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        $this->authorize('update', $group);
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $this->authorize('update', $group);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $group->update($request->all());

        return redirect()->route('groups.show', $group)
            ->with('success', 'Группа успешно обновлена');
    }

    public function destroy(Group $group)
    {
        $this->authorize('delete', $group);
        $group->delete();
        return redirect()->route('groups.index')
            ->with('success', 'Группа успешно удалена');
    }
}

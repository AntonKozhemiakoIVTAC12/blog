<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComponentRequest;
use App\Models\Component;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function create()
    {
        return view('constructor.create');
    }
    /**
     * Создание нового компонента.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreComponentRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['order'] = $validated['order'] ?? 0;

        Component::create($validated);

        return redirect()->route('articles.index');
    }
}

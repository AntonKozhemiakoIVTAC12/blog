<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComponentRequest;
use App\Models\Component;
use Illuminate\Support\Str;

class ComponentController extends Controller
{
    public function create()
    {
        return view('constructor.create');
    }

    /**
     * Создание нового компонента.
     *
     * @param StoreComponentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreComponentRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['order'] = $validated['order'] ?? 0;

        if (empty($validated['key'])) {
            $validated['key'] = $this->generateUniqueKey($validated['standard_key']);
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('components/images', 'public');
            $validated['image'] = $imagePath;
        }

        Component::create($validated);

        return redirect()->route('articles.index')->with('success', 'Компонент успешно создан!');
    }

    /**
     * Генерирует уникальный ключ для компонента.
     *
     * @param string $standardKey
     * @return string
     */
    protected function generateUniqueKey(string $standardKey): string
    {
        do {
            $key = Str::slug($standardKey . '-' . Str::random(8));
        } while (Component::where('key', $key)->where('user_id', auth()->id())->exists());

        return $key;
    }
}

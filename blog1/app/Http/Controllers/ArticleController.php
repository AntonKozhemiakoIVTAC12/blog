<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Component;
use App\Models\Group;
use App\Traits\GostFieldsTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use GostFieldsTrait;

    private array $standards = [
        'gost34' => 'ГОСТ 34',
        'gost19' => 'ГОСТ 19',
        'ieee830' => 'IEEE STD 830-1998',
        'iso29148' => 'ISO/IEC/IEEE 29148-2011'
    ];
    public function index(Request $request)
    {
        $user = auth()->user();
        $searchQuery = $request->input('query');
        $groupFilter = $request->input('group_filter');

        $articles = Article::whereHas('group', function($query) use ($user, $groupFilter) {
            $query->whereIn('id', $user->groups()->pluck('id'))
                ->when($groupFilter, function($q) use ($groupFilter) {
                    $q->where('id', $groupFilter);
                });
        })
            ->with(['user', 'group'])
            ->search($searchQuery)
            ->latest()
            ->paginate(10);

        return view('articles.index', compact('articles', 'searchQuery'));
    }

    public function activeGroup()
    {
        return $this->groups()->latest()->first();
    }

    public function create()
    {
        $user = auth()->user();

        $groups = $user->groups()->get();
        abort_if($groups->isEmpty(), 403, 'Вы не состоите ни в одной группе');

        return view('articles.create', [
            'groups' => $groups,
            'standards' => $this->standards,
            'defaultComponents' => Component::where('user_id', auth()->id())
                ->where('standard_key', 'gost34')
                ->get()
        ]);
    }

    public function store(StoreArticleRequest $request)
    {
        $validated = $request->validated();

        Article::create([
            'title' => $validated['title'],
            'standard' => $validated['standard'],
            'user_id' => auth()->id(),
            'group_id' => $validated['group_id'],
            'gost_data' => json_decode($validated['gost_data_serialized'], true)
        ]);

        return redirect()->route('articles.index')
            ->with('success', 'Документ успешно создан');
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $this->authorize('update', $article);

        $validated = $request->validated();

        $article->update([
            'title' => $validated['title'],
            'standard' => $validated['standard'],
            'gost_data' => json_decode($validated['gost_data_serialized'], true)
        ]);

        return redirect()->route('articles.index')
            ->with('success', 'Документ успешно обновлен');
    }

    public function getComponentsJson($standard)
    {
        $components = Component::where('user_id', auth()->id())
            ->where('standard_key', $standard)
            ->orderBy('order')
            ->get();

        return response()->json($components);
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Документ успешно удален');
    }

    public function getGostFieldsJson($standard)
    {
        return response()->json($this->getGostFields($standard));
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);

        $defaultComponents = Component::where('user_id', auth()->id())
            ->where('standard_key', 'gost34')
            ->get();

        $selectedComponents = $article->gost_data ? array_keys($article->gost_data) : [];

        return view('articles.edit', [
            'article' => $article,
            'standards' => $this->standards,
            'defaultComponents' => $defaultComponents,
            'selectedComponents' => $selectedComponents,
        ]);
    }

    public function show(Article $article)
    {
        $this->authorize('view', $article);

        $gostFields = $this->getGostFields($article->standard);

        $filteredGostData = [];
        if ($article->gost_data) {
            foreach ($article->gost_data as $key => $value) {
                if (isset($gostFields[$key])) {
                    $filteredGostData[$key] = $value;
                }
            }
        }

        return view('articles.show', [
            'article' => $article,
            'gostFields' => $gostFields,
            'filteredGostData' => $filteredGostData,
            'standards' => $this->standards,
        ]);
    }

    // В контроллере
    public function exportPdf(Article $article)
    {
        if ($article->user_id !== auth()->id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('articles.pdf', [
            'article' => $article,
        ]);

        $pdf->setOption([
            'font_cache' => storage_path('fonts/'),
            'default_font' => 'dejavu sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'dpi' => 300,
            'defaultPaperSize' => 'A4',
            'font_height_ratio' => 0.9
        ]);

        return $pdf->download("{$article->title}.pdf");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use App\Models\Component;
use App\Traits\GostFieldsTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleAdminController extends Controller
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
        $searchQuery = $request->input('query');

        $articles = Article::where('user_id', auth()->id())
            ->search($searchQuery)
            ->latest()
            ->paginate(5);

        return view('admin.articles.index', compact('articles', 'searchQuery'));
    }

    public function create()
    {
        return view('admin.articles.create', [
            'standards' => $this->standards,
            'defaultComponents' => Component::where('user_id', auth()->id())
                ->where('standard_key', 'gost34')
                ->get()
        ]);
    }

    public function store(StoreArticleRequest $request)
    {
        Article::create($request->validated());

        return redirect()->route('admin.articles.index');
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $article->update($request->validated());

        return redirect()->route('admin.articles.index')->with('success', 'Документ успешно обновлен.');
    }

    public function edit(Article $article)
    {
        $defaultComponents = Component::where('user_id', auth()->id())
            ->where('standard_key', 'gost34')
            ->get();

        return view('admin.articles.edit', [
            'article' => $article,
            'standards' => $this->standards,
            'defaultComponents' => $defaultComponents,
            'selectedComponents' => $article->gost_data ? array_keys($article->gost_data) : [],
        ]);
    }

    public function show(Article $article)
    {
        $gostFields = $this->getGostFields($article->standard);

        $filteredGostData = [];
        if ($article->gost_data) {
            foreach ($article->gost_data as $key => $value) {
                if (isset($gostFields[$key])) {
                    $filteredGostData[$key] = $value;
                }
            }
        }

        return view('admin.articles.show', [
            'article' => $article,
            'gostFields' => $gostFields,
            'filteredGostData' => $filteredGostData
        ]);
    }

    public function getComponentsJson($standard)
    {
        $components = Component::where('standard_key', $standard)
            ->orderBy('order')
            ->get();

        return response()->json($components);
    }

    public function getGostFieldsJson($standard)
    {
        return response()->json($this->getGostFields($standard));
    }

    public function exportPdf(Article $article)
    {
        if ($article->user_id !== auth()->id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('articles.pdf', [
            'article' => $article,
        ]);

        return $pdf->download("{$article->title}.pdf");
    }
}

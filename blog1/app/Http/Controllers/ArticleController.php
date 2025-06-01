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
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;

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
            ->paginate(5);

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

        $gostData = json_decode($validated['gost_data_serialized'], true);

        $gostData = array_map(function($item) {
            return [
                'key' => $item['key'],
                'content' => str_replace(["\n", "\r"], '', $item['content'] ?? '')
            ];
        }, $gostData);

        Article::create([
            'title' => $validated['title'],
            'standard' => $validated['standard'],
            'user_id' => auth()->id(),
            'group_id' => $validated['group_id'],
            'gost_data' => $gostData
        ]);

        return redirect()->route('articles.index')
            ->with('success', 'Документ успешно создан');
    }

    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $path = $request->file('image')->store('public/images');
            $url = Storage::url($path);

            return response()->json([
                'location' => $url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateArticleRequest $request, Article $article)
    {
        $this->authorize('update', $article);

        $validated = $request->validated();

        $gostData = json_decode($validated['gost_data_serialized'], true);

        $gostData = array_map(function($item) {
            return [
                'key' => $item['key'],
                'content' => str_replace(["\n", "\r"], '', $item['content'] ?? '')
            ];
        }, $gostData);

        $article->update([
            'title' => $validated['title'],
            'standard' => $validated['standard'],
            'gost_data' => $gostData
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
            ->where('standard_key', $article->standard)
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

        ini_set('max_execution_time', 300);
        ini_set('pcre.backtrack_limit', '5000000');
        ini_set('memory_limit', '1024M');

        $pdf = Pdf::loadView('articles.pdf', [
            'article' => $article->load('user')
        ]);

        $pdf->setOption([
            'enable_php' => false,
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultPaperSize' => 'A4',
            'dpi' => 96,
            'fontHeightRatio' => 1,
            'defaultFont' => 'dejavu sans',
            'fontDir' => storage_path('fonts/'),
            'fontCache' => storage_path('fonts/'),
            'tempDir' => storage_path('app/temp/'),
            'encoding' => 'UTF-8',
        ]);

        return $pdf->download("{$article->title}.pdf");
    }

    public function exportDocx(Article $article)
    {
        if ($article->user_id !== auth()->id()) {
            abort(403);
        }

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Добавляем заголовок документа
        $section->addTitle($article->title, 1);

        // Обрабатываем содержимое статьи
        if (isset($article->gost_data) && is_array($article->gost_data)) {
            foreach ($article->gost_data as $item) {
                $key = $item['key'] ?? 'Без названия';
                $content = $item['content'] ?? '';

                // Добавляем подзаголовок раздела
                $section->addTitle($key, 2);

                // Конвертируем HTML в текст для Word
                $cleanContent = htmlspecialchars_decode(html_entity_decode($content, ENT_QUOTES, 'UTF-8'), ENT_QUOTES);

                Html::addHtml($section, $cleanContent);

                $section->addPageBreak();
            }
        }

        $fileName = "{$article->title}.docx";
        $tempFile = tempnam(sys_get_temp_dir(), 'word') . '.docx';

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}

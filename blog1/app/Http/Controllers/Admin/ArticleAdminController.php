<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ArticleAdmin;
use Illuminate\Support\Str;
use App\Models\Article;
class ArticleAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {// Извлекаем статьи только для текущего аутентифицированного пользователя
        $articles = Article::all();
        //dd($articles);
        return view('admin.articles.index', compact('articles'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function postSearch(Request $request)
    {
        $q = $request->input('query');

        $articles = Article::where('title', 'like', '%' . $q . '%')
            ->orWhere('content', 'like', '%' . $q . '%')
            ->get();

        return view('admin.articles.index', compact('articles', 'q'));
    }
    public function create(){
        $categories = Category::orderBy('created_at', 'DESC')->get();
        return view('admin.articles.create', [
            'categories' => $categories
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){

        //dd($request->all());

        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);
        Article::create([
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'content' => $request->input('content'),
            'user_id' => auth()->id(),
        ]);
        return redirect()->back()->withSuccess('Категория успешно добавлена');
        //return redirect()->route('articles.index')->with('success', 'Статья успешно создана.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {

        return view('admin.articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
//        dd($request->all(), $article);
        $request -> validate([
            'title'=>'required',
            'content'=>'required',
        ]);

        $article->update([
            'title'=> $request->title,
            'slug'=> Str::slug($request->title),
            'content'=> $request->input('content'),
        ]);

        return redirect()->route('admin.articles.show', ['article' => $article])->with('success', 'Статья успешно обновлена.');


    }


    public function destroy(Article $article)
    {
        $article->delete();
        return redirect('/admin_panel/admin/articles')->with('success', 'Статья успешно удалена.');
    }

}

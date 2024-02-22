<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Category;
class ArticleController extends Controller
{
    public function index()
    {// Извлекаем статьи только для текущего аутентифицированного пользователя
        $articles = Article::where('user_id', auth()->id())->latest()->get();
        //dd($articles);
        return view('articles.index', compact('articles'));
    }


    public function postSearch(Request $request)
    {
        $q = $request->input('query');

        $articles = Article::where('title', 'like', '%' . $q . '%')
            ->orWhere('content', 'like', '%' . $q . '%')
            ->get();

        return view('articles.index', compact('articles', 'q'));
    }

    public function create(){
        $categories = Category::orderBy('created_at', 'DESC')->get();
        return view('articles.create', [
            'categories' => $categories
        ]);
    }
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

    public function show(Article $article)
    {
        return view('articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $request -> validate([
            'title'=>'required',
            'content'=>'required',
        ]);

        $article->update([
            'title'=> $request->title,
            'slug'=> Str::slug($request->title),
            'content'=> $request->input('content'),
        ]);

        return redirect()->route('articles.show', $article)->with('success', 'Статья успешно обновлена.');
    }


    public function delete(Article $article)
    {
        $article -> delete();
        return redirect()->route('articles.index')->with('success', 'Статья успешно удалена.');
    }

}

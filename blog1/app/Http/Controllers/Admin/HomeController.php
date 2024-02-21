<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\ArticleController;

class HomeController extends Controller
{
    public function index()
    {
        $article_count = Article::count();
        return view('admin.home.index',[
            'article_count' => $article_count
        ]);
    }
}

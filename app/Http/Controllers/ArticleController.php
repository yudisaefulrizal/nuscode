<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::orderByDesc('published_at')->orderByDesc('id')->paginate(12);

        return view('articles.index', compact('articles'));
    }

    public function show(Article $article): View
    {
        return view('articles.show', compact('article'));
    }
}

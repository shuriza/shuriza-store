<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('author')
            ->latestPublished()
            ->paginate(12);

        return view('articles-index', compact('articles'));
    }

    public function show(Article $article)
    {
        if (!$article->is_published && !(auth()->check() && auth()->user()->isAdmin())) {
            abort(404);
        }

        $article->increment('views');

        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('articles-show', compact('article', 'relatedArticles'));
    }
}

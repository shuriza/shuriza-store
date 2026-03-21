<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('author')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $articles = $query->paginate(15)->withQueryString();

        return view('admin.articles-index', compact('articles'));
    }

    public function create()
    {
        return view('admin.articles-form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:200',
            'slug'         => 'nullable|string|max:200|unique:articles,slug',
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_published' => 'boolean',
        ], [
            'title.required' => 'Judul artikel wajib diisi.',
            'image.image'    => 'File harus berupa gambar.',
            'image.max'      => 'Ukuran gambar maksimal 5MB.',
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['user_id'] = auth()->id();

        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        Article::create($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil ditambahkan!');
    }

    public function edit(Article $article)
    {
        return view('admin.articles-form', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:200',
            'slug'         => 'nullable|string|max:200|unique:articles,slug,' . $article->id,
            'excerpt'      => 'nullable|string|max:500',
            'body'         => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if ($validated['is_published'] && !$article->published_at) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }
            $validated['image'] = $request->file('image')->store('articles', 'public');
        }

        if ($request->boolean('remove_image') && $article->image) {
            Storage::disk('public')->delete($article->image);
            $validated['image'] = null;
        }

        $article->update($validated);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil diperbarui!');
    }

    public function destroy(Article $article)
    {
        if ($article->image) {
            Storage::disk('public')->delete($article->image);
        }

        $article->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    public function togglePublish(Article $article)
    {
        $article->update([
            'is_published' => !$article->is_published,
            'published_at' => !$article->is_published ? ($article->published_at ?? now()) : $article->published_at,
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success'      => true,
                'is_published' => $article->is_published,
                'message'      => $article->is_published ? 'Artikel dipublikasikan.' : 'Artikel disembunyikan.',
            ]);
        }

        return back()->with('success', $article->is_published ? 'Artikel dipublikasikan.' : 'Artikel disembunyikan.');
    }
}

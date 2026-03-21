<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['products', 'activeProducts'])
            ->ordered()
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100|unique:categories,name',
            'slug'        => 'nullable|string|max:120|unique:categories,slug',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah digunakan.',
        ]);

        $validated['slug']       = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$validated['name']}\" berhasil ditambahkan!");
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => "required|string|max:100|unique:categories,name,{$category->id}",
            'slug'        => "nullable|string|max:120|unique:categories,slug,{$category->id}",
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique'   => 'Nama kategori sudah digunakan.',
        ]);

        $validated['slug']      = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diperbarui!");
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with(
                'error',
                "Kategori \"{$category->name}\" tidak bisa dihapus karena masih memiliki produk."
            );
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$name}\" berhasil dihapus.");
    }

    public function toggleActive(Category $category)
    {
        $category->update(['is_active' => ! $category->is_active]);

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Kategori \"{$category->name}\" berhasil {$status}.");
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders'   => 'required|array',
            'orders.*' => 'integer|exists:categories,id',
        ]);

        foreach ($request->orders as $sortOrder => $id) {
            Category::where('id', $id)->update(['sort_order' => $sortOrder]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan kategori diperbarui.']);
    }
}

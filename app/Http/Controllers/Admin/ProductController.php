<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products   = $query->paginate(15)->withQueryString();
        $categories = Category::active()->ordered()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:200',
            'slug'              => 'nullable|string|max:200|unique:products,slug',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:300',
            'price'             => 'required|integer|min:0',
            'original_price'    => 'nullable|integer|min:0',
            'stock'             => 'required|integer|min:0',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'badge'             => 'nullable|in:hot,sale,new',
            'is_active'         => 'boolean',
            'is_popular'        => 'boolean',
            'sort_order'        => 'integer|min:0',
            'flash_sale_price'  => 'nullable|integer|min:0',
            'flash_sale_start'  => 'nullable|date',
            'flash_sale_end'    => 'nullable|date|after:flash_sale_start',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'name.required'        => 'Nama produk wajib diisi.',
            'price.required'       => 'Harga produk wajib diisi.',
            'stock.required'       => 'Stok produk wajib diisi.',
            'image.image'          => 'File harus berupa gambar.',
            'image.max'            => 'Ukuran gambar maksimal 2MB.',
        ]);

        // Generate slug if empty
        if (empty($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        }

        // Handle checkboxes
        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['sort_order'] = $request->input('sort_order', 0);

        // Upload image
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Produk \"{$product->name}\" berhasil ditambahkan!");
    }

    /**
     * Show the form for editing a product.
     */
    public function edit(Product $product)
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:200',
            'slug'              => 'nullable|string|max:200|unique:products,slug,' . $product->id,
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:300',
            'price'             => 'required|integer|min:0',
            'original_price'    => 'nullable|integer|min:0',
            'stock'             => 'required|integer|min:0',
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'badge'             => 'nullable|in:hot,sale,new',
            'is_active'         => 'boolean',
            'is_popular'        => 'boolean',
            'sort_order'        => 'integer|min:0',
            'flash_sale_price'  => 'nullable|integer|min:0',
            'flash_sale_start'  => 'nullable|date',
            'flash_sale_end'    => 'nullable|date|after:flash_sale_start',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'name.required'        => 'Nama produk wajib diisi.',
            'price.required'       => 'Harga produk wajib diisi.',
            'stock.required'       => 'Stok produk wajib diisi.',
        ]);

        // Slug fallback
        if (empty($validated['slug'])) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name'], $product->id);
        }

        $validated['is_active']  = $request->boolean('is_active');
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['sort_order'] = $request->input('sort_order', 0);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Hapus gambar jika diminta
        if ($request->boolean('remove_image') && $product->image) {
            Storage::disk('public')->delete($product->image);
            $validated['image'] = null;
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Produk \"{$product->name}\" berhasil diperbarui!");
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        $name = $product->name;

        // Hapus gambar dari storage
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', "Produk \"{$name}\" berhasil dihapus.");
    }

    /**
     * Toggle active status (AJAX).
     */
    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $product->is_active,
            'message'   => $product->is_active
                ? "Produk \"{$product->name}\" diaktifkan."
                : "Produk \"{$product->name}\" dinonaktifkan.",
        ]);
    }

    /**
     * Toggle popular status (AJAX).
     */
    public function togglePopular(Product $product)
    {
        $product->update(['is_popular' => ! $product->is_popular]);

        return response()->json([
            'success'    => true,
            'is_popular' => $product->is_popular,
            'message'    => $product->is_popular
                ? "Produk \"{$product->name}\" ditandai populer."
                : "Produk \"{$product->name}\" dihapus dari populer.",
        ]);
    }

    /**
     * Bulk actions (delete, activate, deactivate).
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'ids'    => 'required|array',
            'ids.*'  => 'exists:products,id',
        ]);

        $products = Product::whereIn('id', $request->ids)->get();

        switch ($request->action) {
            case 'delete':
                foreach ($products as $product) {
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $product->delete();
                }
                $message = count($request->ids) . ' produk berhasil dihapus.';
                break;

            case 'activate':
                Product::whereIn('id', $request->ids)->update(['is_active' => true]);
                $message = count($request->ids) . ' produk diaktifkan.';
                break;

            case 'deactivate':
                Product::whereIn('id', $request->ids)->update(['is_active' => false]);
                $message = count($request->ids) . ' produk dinonaktifkan.';
                break;
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', $message);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug  = Str::slug($name);
        $count = 1;

        while (
            Product::where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = Str::slug($name) . '-' . $count;
            $count++;
        }

        return $slug;
    }
}

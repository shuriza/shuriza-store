<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    protected array $gradientOptions = [
        'bg-gradient-to-br from-peri to-peri-dark'       => 'Ungu (Default)',
        'bg-gradient-to-br from-pink-500 to-purple-600'  => 'Pink → Ungu',
        'bg-gradient-to-br from-blue-500 to-peri'        => 'Biru → Ungu',
        'bg-gradient-to-br from-green-500 to-teal-600'   => 'Hijau → Teal',
        'bg-gradient-to-br from-amber-500 to-orange-600' => 'Kuning → Orange',
        'bg-gradient-to-br from-red-500 to-pink-600'     => 'Merah → Pink',
        'bg-gradient-to-br from-indigo-500 to-blue-600'  => 'Indigo → Biru',
        'bg-gradient-to-br from-gray-700 to-gray-900'    => 'Abu-abu Gelap',
    ];

    public function index()
    {
        $banners = Banner::ordered()->get();
        return view('admin.banners-index', compact('banners'));
    }

    public function create()
    {
        $gradients = $this->gradientOptions;
        return view('admin.banners-form', compact('gradients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'      => 'nullable|string|max:200',
            'subtitle'   => 'nullable|string|max:300',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'link'       => 'nullable|url|max:500',
            'gradient'   => 'nullable|string|max:200',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ], [
            'image.image' => 'File harus berupa gambar.',
            'image.max'   => 'Ukuran gambar maksimal 5MB.',
            'link.url'    => 'Link harus berupa URL yang valid.',
        ]);

        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['sort_order'] = $request->input('sort_order', 0);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($validated);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan!');
    }

    public function edit(Banner $banner)
    {
        $gradients = $this->gradientOptions;
        return view('admin.banners-form', compact('banner', 'gradients'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title'      => 'nullable|string|max:200',
            'subtitle'   => 'nullable|string|max:300',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'link'       => 'nullable|url|max:500',
            'gradient'   => 'nullable|string|max:200',
            'sort_order' => 'integer|min:0',
            'is_active'  => 'boolean',
        ]);

        $validated['is_active']  = $request->boolean('is_active');
        $validated['sort_order'] = $request->input('sort_order', 0);

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        if ($request->boolean('remove_image') && $banner->image) {
            Storage::disk('public')->delete($banner->image);
            $validated['image'] = null;
        }

        $banner->update($validated);

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil diperbarui!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()
            ->route('admin.banners.index')
            ->with('success', 'Banner berhasil dihapus.');
    }

    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        if (request()->expectsJson()) {
            return response()->json([
                'success'   => true,
                'is_active' => $banner->is_active,
                'message'   => $banner->is_active ? 'Banner diaktifkan.' : 'Banner dinonaktifkan.',
            ]);
        }

        return back()->with('success', $banner->is_active ? 'Banner diaktifkan.' : 'Banner dinonaktifkan.');
    }
}

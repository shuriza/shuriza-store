@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')
@section('breadcrumb')
    <a href="{{ route('admin.products.index') }}" class="hover:text-white transition">Produk</a>
    <i class="fas fa-chevron-right text-[0.5rem] mx-1"></i> Edit
@endsection

@section('content')
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data"
      x-data="{
          name: '{{ old('name', $product->name) }}',
          slug: '{{ old('slug', $product->slug) }}',
          slugManual: true,
          imagePreview: {{ $product->image_url ? "'" . $product->image_url . "'" : 'null' }},
          toSlug(str) {
              return str.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/[\s]+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
          }
      }">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ══ LEFT COLUMN (7/12) ══ --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- Informasi Produk --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-info-circle text-peri"></i> Informasi Produk
                    </h2>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Nama Produk <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                               x-model="name"
                               @input="if (!slugManual) slug = toSlug(name)"
                               value="{{ old('name', $product->name) }}"
                               placeholder="Contoh: Netflix Premium 1 Bulan"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Slug URL
                            <button type="button" @click="slugManual = !slugManual"
                                    class="text-xs ml-2" :class="slugManual ? 'text-yellow-400' : 'text-peri'">
                                <i class="fas" :class="slugManual ? 'fa-lock' : 'fa-lock-open'"></i>
                                <span x-text="slugManual ? 'Manual' : 'Otomatis'"></span>
                            </button>
                        </label>
                        <input type="text" id="slug" name="slug"
                               x-model="slug"
                               @input="slugManual = true"
                               placeholder="netflix-premium-1-bulan"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-link mr-1"></i>{{ url('/produk/') }}/<span class="text-peri" x-text="slug || '—'"></span></p>
                        @error('slug') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Deskripsi Lengkap
                        </label>
                        <textarea id="description" name="description" rows="8"
                                  placeholder="Detail produk, fitur, cara penggunaan..."
                                  class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent resize-y">{{ old('description', $product->description) }}</textarea>
                        @error('description') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Short Description --}}
                    <div>
                        <label for="short_description" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Deskripsi Singkat
                        </label>
                        <textarea id="short_description" name="short_description" rows="3"
                                  placeholder="Ringkasan singkat produk..."
                                  class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent resize-y">{{ old('short_description', $product->short_description) }}</textarea>
                        @error('short_description') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>

        </div>

        {{-- ══ RIGHT COLUMN (5/12) ══ --}}
        <div class="lg:col-span-5 space-y-6">

            {{-- Harga & Stok --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-tag text-peri"></i> Harga &amp; Stok
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Harga (IDR) <span class="text-red-400">*</span>
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 bg-gray-700 border border-r-0 border-gray-700 rounded-l-xl text-sm text-gray-400">Rp</span>
                            <input type="number" id="price" name="price" required min="0"
                                   value="{{ old('price', $product->price) }}" placeholder="45000"
                                   class="flex-1 bg-gray-800 border border-gray-700 rounded-r-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        </div>
                        @error('price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="original_price" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Harga Asli <span class="text-gray-500 text-xs">(opsional, dicoret)</span>
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 bg-gray-700 border border-r-0 border-gray-700 rounded-l-xl text-sm text-gray-400">Rp</span>
                            <input type="number" id="original_price" name="original_price" min="0"
                                   value="{{ old('original_price', $product->original_price) }}" placeholder="99000"
                                   class="flex-1 bg-gray-800 border border-gray-700 rounded-r-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        </div>
                        @error('original_price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Stok <span class="text-red-400">*</span>
                        </label>
                        <input type="number" id="stock" name="stock" required min="0"
                               value="{{ old('stock', $product->stock) }}" placeholder="100"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        @error('stock') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Flash Sale --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-bolt text-yellow-400"></i> Flash Sale (Opsional)
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label for="flash_sale_price" class="block text-sm font-medium text-gray-300 mb-1.5">Harga Flash Sale (IDR)</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-4 bg-gray-700 border border-r-0 border-gray-700 rounded-l-xl text-gray-400 text-sm">Rp</span>
                            <input type="number" id="flash_sale_price" name="flash_sale_price" min="0"
                                   value="{{ old('flash_sale_price', $product->flash_sale_price) }}" placeholder="25000"
                                   class="flex-1 bg-gray-800 border border-gray-700 rounded-r-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        </div>
                        @error('flash_sale_price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="flash_sale_start" class="block text-sm font-medium text-gray-300 mb-1.5">Mulai</label>
                            <input type="datetime-local" id="flash_sale_start" name="flash_sale_start"
                                   value="{{ old('flash_sale_start', $product->flash_sale_start?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-peri focus:border-transparent">
                            @error('flash_sale_start') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="flash_sale_end" class="block text-sm font-medium text-gray-300 mb-1.5">Berakhir</label>
                            <input type="datetime-local" id="flash_sale_end" name="flash_sale_end"
                                   value="{{ old('flash_sale_end', $product->flash_sale_end?->format('Y-m-d\TH:i')) }}"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-peri focus:border-transparent">
                            @error('flash_sale_end') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    @if($product->is_flash_sale)
                        <div class="flex items-center gap-2 px-3 py-2 bg-red-500/10 border border-red-500/20 rounded-xl">
                            <i class="fas fa-bolt text-red-400"></i>
                            <span class="text-sm text-red-400 font-medium">Flash Sale aktif sampai {{ $product->flash_sale_end->format('d M Y, H:i') }}</span>
                        </div>
                    @endif
                    <p class="text-xs text-gray-500">Kosongkan jika tidak ingin mengaktifkan flash sale.</p>
                </div>
            </div>

            {{-- Kategori & Badge --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-tags text-peri"></i> Kategori &amp; Badge
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Kategori <span class="text-red-400">*</span>
                        </label>
                        <select id="category_id" name="category_id" required
                                class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-peri focus:border-transparent">
                            <option value="">— Pilih Kategori —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="badge" class="block text-sm font-medium text-gray-300 mb-1.5">Badge</label>
                        <select id="badge" name="badge"
                                class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-peri focus:border-transparent">
                            <option value="" {{ old('badge', $product->badge) == '' ? 'selected' : '' }}>Tidak ada</option>
                            <option value="hot" {{ old('badge', $product->badge) == 'hot' ? 'selected' : '' }}>🔥 Hot</option>
                            <option value="sale" {{ old('badge', $product->badge) == 'sale' ? 'selected' : '' }}>💰 Sale</option>
                            <option value="new" {{ old('badge', $product->badge) == 'new' ? 'selected' : '' }}>✨ New</option>
                        </select>
                        @error('badge') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="badge_label" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Label Badge <span class="text-gray-500 text-xs">(opsional)</span>
                        </label>
                        <input type="text" id="badge_label" name="badge_label"
                               value="{{ old('badge_label', $product->badge_label) }}" placeholder="Teks custom badge"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        @error('badge_label') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Media --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-image text-peri"></i> Media
                    </h2>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Gambar Produk</label>
                    <div class="relative border-2 border-dashed border-gray-700 rounded-xl p-6 text-center hover:border-peri transition cursor-pointer"
                         @click="$refs.imageInput.click()">
                        <input type="file" name="image" accept="image/*" class="hidden" x-ref="imageInput"
                               @change="if ($event.target.files[0]) { const r = new FileReader(); r.onload = e => imagePreview = e.target.result; r.readAsDataURL($event.target.files[0]); }">
                        <template x-if="!imagePreview">
                            <div>
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-500 mb-2"></i>
                                <p class="text-sm text-gray-400">Klik untuk upload gambar baru</p>
                                <p class="text-xs text-gray-600 mt-1">PNG, JPG, WEBP maks 2MB</p>
                            </div>
                        </template>
                        <template x-if="imagePreview">
                            <div class="relative">
                                <img :src="imagePreview" class="mx-auto max-h-48 rounded-lg object-contain">
                                <button type="button" @click.stop="imagePreview = null; $refs.imageInput.value = ''"
                                        class="absolute top-1 right-1 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    @error('image') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Pengaturan --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-cog text-peri"></i> Pengaturan
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Active toggle --}}
                    <label class="flex items-center justify-between cursor-pointer group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-green-500/10 flex items-center justify-center">
                                <i class="fas fa-eye text-green-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">Produk Aktif</p>
                                <p class="text-xs text-gray-500">Tampilkan di halaman toko</p>
                            </div>
                        </div>
                        <div class="relative" x-data="{ on: {{ old('is_active', $product->is_active) ? 'true' : 'false' }} }">
                            <input type="hidden" name="is_active" :value="on ? 1 : 0">
                            <button type="button" @click="on = !on"
                                    :class="on ? 'bg-peri' : 'bg-gray-700'"
                                    class="relative w-11 h-6 rounded-full transition-colors">
                                <span :class="on ? 'translate-x-5' : 'translate-x-0.5'"
                                      class="inline-block w-5 h-5 bg-white rounded-full transform transition-transform shadow"></span>
                            </button>
                        </div>
                    </label>

                    {{-- Popular toggle --}}
                    <label class="flex items-center justify-between cursor-pointer group">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-white">Produk Populer</p>
                                <p class="text-xs text-gray-500">Tampilkan di bagian populer</p>
                            </div>
                        </div>
                        <div class="relative" x-data="{ on: {{ old('is_popular', $product->is_popular) ? 'true' : 'false' }} }">
                            <input type="hidden" name="is_popular" :value="on ? 1 : 0">
                            <button type="button" @click="on = !on"
                                    :class="on ? 'bg-peri' : 'bg-gray-700'"
                                    class="relative w-11 h-6 rounded-full transition-colors">
                                <span :class="on ? 'translate-x-5' : 'translate-x-0.5'"
                                      class="inline-block w-5 h-5 bg-white rounded-full transform transition-transform shadow"></span>
                            </button>
                        </div>
                    </label>
                </div>
            </div>

        </div>
    </div>

    {{-- Submit buttons --}}
    <div class="flex items-center justify-end gap-3 mt-8">
        <a href="{{ route('admin.products.index') }}"
           class="px-6 py-3 rounded-xl border border-gray-700 text-gray-400 hover:text-white hover:border-gray-500 transition text-sm font-medium">
            Batal
        </a>
        <button type="submit"
                class="px-6 py-3 rounded-xl bg-peri hover:bg-peri-dark text-white font-semibold text-sm transition flex items-center gap-2">
            <i class="fas fa-save"></i> Simpan Perubahan
        </button>
    </div>
</form>
@endsection

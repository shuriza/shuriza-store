@extends('layouts.admin')

@section('title', isset($article) ? 'Edit Artikel' : 'Tulis Artikel')
@section('page-title', isset($article) ? 'Edit Artikel' : 'Tulis Artikel')
@section('breadcrumb')
    <a href="{{ route('admin.articles.index') }}" class="hover:text-white transition">Artikel</a>
    <i class="fas fa-chevron-right text-[0.5rem] mx-1"></i> {{ isset($article) ? 'Edit' : 'Tulis' }}
@endsection

@section('content')
<form action="{{ isset($article) ? route('admin.articles.update', $article) : route('admin.articles.store') }}"
      method="POST" enctype="multipart/form-data"
      x-data="{
          title: '{{ old('title', addslashes($article->title ?? '')) }}',
          slug: '{{ old('slug', $article->slug ?? '') }}',
          slugManual: {{ old('slug', ($article ?? null)?->slug) ? 'true' : 'false' }},
          imagePreview: {{ isset($article) && $article->image ? '\'' . $article->image_url . '\'' : 'null' }},
          removeImage: false,
          toSlug(str) {
              return str.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/[\s]+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
          }
      }">
    @csrf
    @if(isset($article)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ══ LEFT COLUMN (8/12) ══ --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Informasi Artikel --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-pen-fancy text-peri"></i> Informasi Artikel
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Judul Artikel <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="title" name="title" required
                               x-model="title"
                               @input="if (!slugManual) slug = toSlug(title)"
                               value="{{ old('title', $article->title ?? '') }}"
                               placeholder="Contoh: Tips Memilih Produk Digital yang Tepat"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        @error('title') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Slug URL <span class="text-gray-500 text-xs">(otomatis dari judul)</span>
                        </label>
                        <input type="text" id="slug" name="slug"
                               x-model="slug"
                               @input="slugManual = (slug !== toSlug(title))"
                               placeholder="tips-memilih-produk-digital"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        @error('slug') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Excerpt --}}
                    <div>
                        <label for="excerpt" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Ringkasan <span class="text-gray-500 text-xs">(ditampilkan di daftar artikel)</span>
                        </label>
                        <textarea id="excerpt" name="excerpt" rows="3"
                                  placeholder="Tulis ringkasan singkat artikel ini..."
                                  class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent resize-y">{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                        @error('excerpt') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Isi Artikel --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-align-left text-peri"></i> Isi Artikel
                    </h2>
                </div>
                <div class="p-6">
                    <textarea id="body" name="body" rows="18"
                              placeholder="Tulis isi artikel di sini... Mendukung HTML sederhana."
                              class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent resize-y font-mono text-sm leading-relaxed">{{ old('body', $article->body ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i> Bisa menggunakan tag HTML sederhana: &lt;b&gt;, &lt;i&gt;, &lt;a&gt;, &lt;p&gt;, &lt;h2&gt;, &lt;h3&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;br&gt;, &lt;img&gt;</p>
                    @error('body') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ══ RIGHT COLUMN (4/12) ══ --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- Gambar Thumbnail --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-image text-peri"></i> Gambar Thumbnail
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Image Preview --}}
                    <div x-show="imagePreview && !removeImage"
                         class="relative rounded-xl overflow-hidden border border-gray-700">
                        <img :src="imagePreview" class="w-full h-40 object-cover" alt="Preview">
                        <button type="button" @click="removeImage = true; imagePreview = null"
                                class="absolute top-2 right-2 rounded-full bg-red-600/80 p-2 text-white hover:bg-red-600 transition">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    {{-- Upload --}}
                    <div x-show="!imagePreview || removeImage">
                        <label for="image"
                               class="flex flex-col items-center justify-center h-40 rounded-xl border-2 border-dashed border-gray-700 bg-gray-800/50 cursor-pointer hover:border-peri/50 transition">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-500 mb-2"></i>
                            <span class="text-sm text-gray-400">Upload gambar</span>
                            <span class="text-xs text-gray-500 mt-1">JPG, PNG, WebP — Maks 5MB</span>
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" class="hidden"
                               @change="
                                   const file = $event.target.files[0];
                                   if (file) {
                                       imagePreview = URL.createObjectURL(file);
                                       removeImage = false;
                                   }
                               ">
                    </div>

                    @if(isset($article) && $article->image)
                        <input type="hidden" name="remove_image" :value="removeImage ? 1 : 0">
                    @endif

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
                <div class="p-6 space-y-5">
                    {{-- Published --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1"
                               {{ old('is_published', $article->is_published ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 rounded bg-gray-800 border-gray-600 text-peri focus:ring-peri focus:ring-offset-0">
                        <div>
                            <span class="text-sm font-medium text-white">Publikasikan</span>
                            <p class="text-xs text-gray-500">Tampilkan artikel ini di website</p>
                        </div>
                    </label>

                    @if(isset($article))
                    <div class="space-y-2 text-xs text-gray-500 border-t border-gray-800 pt-4">
                        <div class="flex justify-between">
                            <span>Dibuat</span>
                            <span class="text-gray-400">{{ $article->created_at->translatedFormat('d M Y H:i') }}</span>
                        </div>
                        @if($article->published_at)
                        <div class="flex justify-between">
                            <span>Dipublikasikan</span>
                            <span class="text-gray-400">{{ $article->published_at->translatedFormat('d M Y H:i') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span>Dilihat</span>
                            <span class="text-gray-400">{{ number_format($article->views) }}×</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.articles.index') }}"
                   class="flex-1 rounded-xl bg-white/5 py-3 text-center text-sm font-semibold text-gray-300 transition hover:bg-white/10">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 rounded-xl bg-peri py-3 text-center text-sm font-bold text-white transition hover:bg-peri-dark">
                    <i class="fas fa-save mr-1"></i> {{ isset($article) ? 'Simpan' : 'Terbitkan' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

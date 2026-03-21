@extends('layouts.admin')

@section('title', isset($banner) ? 'Edit Banner' : 'Tambah Banner')
@section('page-title', isset($banner) ? 'Edit Banner' : 'Tambah Banner')
@section('breadcrumb')
    <a href="{{ route('admin.banners.index') }}" class="hover:text-white transition">Banner</a>
    <i class="fas fa-chevron-right text-[0.5rem] mx-1"></i> {{ isset($banner) ? 'Edit' : 'Tambah' }}
@endsection

@section('content')
<form action="{{ isset($banner) ? route('admin.banners.update', $banner) : route('admin.banners.store') }}"
      method="POST" enctype="multipart/form-data"
      x-data="{
          imagePreview: {{ isset($banner) && $banner->image ? '\'' . $banner->image_url . '\'' : 'null' }},
          removeImage: false,
          selectedGradient: '{{ old('gradient', $banner->gradient ?? 'bg-gradient-to-br from-peri to-peri-dark') }}'
      }">
    @csrf
    @if(isset($banner)) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ══ LEFT COLUMN (7/12) ══ --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- Informasi Banner --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-info-circle text-peri"></i> Informasi Banner
                    </h2>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Judul Banner
                        </label>
                        <input type="text" id="title" name="title"
                               value="{{ old('title', $banner->title ?? '') }}"
                               placeholder="Contoh: Selamat Datang di Shuriza Store"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        @error('title') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Subtitle --}}
                    <div>
                        <label for="subtitle" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Subjudul
                        </label>
                        <input type="text" id="subtitle" name="subtitle"
                               value="{{ old('subtitle', $banner->subtitle ?? '') }}"
                               placeholder="Contoh: Toko digital terpercaya di Kediri"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        @error('subtitle') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Link --}}
                    <div>
                        <label for="link" class="block text-sm font-medium text-gray-300 mb-1.5">
                            Link URL <span class="text-gray-500 text-xs">(opsional, klik banner menuju link ini)</span>
                        </label>
                        <input type="url" id="link" name="link"
                               value="{{ old('link', $banner->link ?? '') }}"
                               placeholder="https://..."
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                        @error('link') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Gambar Banner --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-image text-peri"></i> Gambar Banner
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-xs text-gray-400">Ukuran rekomendasi: 1200x400px. Format: JPG, PNG, WebP. Maks 5MB. Jika tidak upload gambar, akan menggunakan gradient warna.</p>

                    {{-- Image Preview --}}
                    <div x-show="imagePreview && !removeImage"
                         class="relative rounded-xl overflow-hidden border border-gray-700">
                        <img :src="imagePreview" class="w-full h-48 object-cover" alt="Preview">
                        <button type="button" @click="removeImage = true; imagePreview = null"
                                class="absolute top-2 right-2 rounded-full bg-red-600/80 p-2 text-white hover:bg-red-600 transition">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    {{-- Upload --}}
                    <div x-show="!imagePreview || removeImage">
                        <label for="image"
                               class="flex flex-col items-center justify-center h-48 rounded-xl border-2 border-dashed border-gray-700 bg-gray-800/50 cursor-pointer hover:border-peri/50 transition">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-500 mb-2"></i>
                            <span class="text-sm text-gray-400">Klik untuk upload gambar</span>
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

                    @if(isset($banner) && $banner->image)
                        <input type="hidden" name="remove_image" :value="removeImage ? 1 : 0">
                    @endif

                    @error('image') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- ══ RIGHT COLUMN (5/12) ══ --}}
        <div class="lg:col-span-5 space-y-6">

            {{-- Gradient Warna --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-palette text-peri"></i> Gradient Warna
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-xs text-gray-400">Gradient digunakan sebagai latar jika tidak ada gambar, atau sebagai overlay.</p>

                    <div class="grid grid-cols-2 gap-3">
                        @foreach($gradients as $class => $label)
                        <label class="cursor-pointer group">
                            <input type="radio" name="gradient" value="{{ $class }}"
                                   x-model="selectedGradient" class="hidden peer">
                            <div class="rounded-xl overflow-hidden border-2 transition
                                        peer-checked:border-peri peer-checked:ring-2 peer-checked:ring-peri/30
                                        border-gray-700 group-hover:border-gray-600">
                                <div class="h-16 {{ $class }}"></div>
                                <div class="bg-gray-800 px-2 py-1.5">
                                    <span class="text-[0.65rem] font-medium text-gray-300">{{ $label }}</span>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
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
                    {{-- Sort Order --}}
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-1.5">Urutan</label>
                        <input type="number" id="sort_order" name="sort_order" min="0"
                               value="{{ old('sort_order', $banner->sort_order ?? 0) }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-peri focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Angka kecil tampil lebih dulu.</p>
                    </div>

                    {{-- Active --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $banner->is_active ?? true) ? 'checked' : '' }}
                               class="w-5 h-5 rounded bg-gray-800 border-gray-600 text-peri focus:ring-peri focus:ring-offset-0">
                        <div>
                            <span class="text-sm font-medium text-white">Aktif</span>
                            <p class="text-xs text-gray-500">Tampilkan banner ini di halaman utama</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Preview --}}
            <div class="bg-gray-900 rounded-2xl border border-gray-800">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-white font-bold flex items-center gap-2">
                        <i class="fas fa-eye text-peri"></i> Preview
                    </h2>
                </div>
                <div class="p-6">
                    <div class="rounded-xl overflow-hidden h-40 relative" :class="(!imagePreview || removeImage) ? selectedGradient : ''">
                        <template x-if="imagePreview && !removeImage">
                            <img :src="imagePreview" class="w-full h-full object-cover" alt="Preview">
                        </template>
                        <template x-if="!imagePreview || removeImage">
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-image text-3xl text-white/20"></i>
                            </div>
                        </template>
                        <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                            <div class="text-center px-4">
                                <p class="text-lg font-bold text-white drop-shadow-lg" x-text="$refs.titleInput?.value || 'Judul Banner'"></p>
                                <p class="text-sm text-white/80 mt-1" x-text="$refs.subtitleInput?.value || 'Subjudul banner'"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3">
                <a href="{{ route('admin.banners.index') }}"
                   class="flex-1 rounded-xl bg-white/5 py-3 text-center text-sm font-semibold text-gray-300 transition hover:bg-white/10">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 rounded-xl bg-peri py-3 text-center text-sm font-bold text-white transition hover:bg-peri-dark">
                    <i class="fas fa-save mr-1"></i> {{ isset($banner) ? 'Simpan Perubahan' : 'Tambah Banner' }}
                </button>
            </div>
        </div>
    </div>
</form>
@endsection

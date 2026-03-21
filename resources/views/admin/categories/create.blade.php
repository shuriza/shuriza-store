@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')
@section('breadcrumb')
    <a href="{{ route('admin.categories.index') }}" class="transition hover:text-white">Kategori</a>
    <i class="fas fa-chevron-right mx-1 text-[0.5rem]"></i> Tambah
@endsection

@section('content')
<form action="{{ route('admin.categories.store') }}" method="POST"
      x-data="{
          name: '{{ old('name') }}',
          slug: '{{ old('slug') }}',
          slugManual: {{ old('slug') ? 'true' : 'false' }},
          toSlug(str) {
              return str.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/[\s]+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
          }
      }">
    @csrf

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

        {{-- ══ LEFT COLUMN ══ --}}
        <div class="space-y-6 lg:col-span-7">
            <div class="rounded-2xl border border-gray-800 bg-gray-900">
                <div class="border-b border-gray-800 px-6 py-4">
                    <h2 class="flex items-center gap-2 font-bold text-white">
                        <i class="fas fa-info-circle text-peri"></i> Informasi Kategori
                    </h2>
                </div>
                <div class="space-y-5 p-6">

                    {{-- Name --}}
                    <div>
                        <label for="name" class="mb-1.5 block text-sm font-medium text-gray-300">
                            Nama Kategori <span class="text-red-400">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                               x-model="name"
                               @input="if (!slugManual) slug = toSlug(name)"
                               placeholder="Contoh: Game Top Up"
                               class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-peri">
                        @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="mb-1.5 block text-sm font-medium text-gray-300">
                            Slug URL <span class="text-xs text-gray-500">(otomatis dari nama)</span>
                        </label>
                        <input type="text" id="slug" name="slug"
                               x-model="slug"
                               @input="slugManual = (slug !== toSlug(name))"
                               placeholder="game-top-up"
                               class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-peri">
                        @error('slug') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="mb-1.5 block text-sm font-medium text-gray-300">Deskripsi</label>
                        <textarea id="description" name="description" rows="4"
                                  placeholder="Deskripsi singkat kategori..."
                                  class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-peri">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ══ RIGHT COLUMN ══ --}}
        <div class="space-y-6 lg:col-span-5">
            <div class="rounded-2xl border border-gray-800 bg-gray-900">
                <div class="border-b border-gray-800 px-6 py-4">
                    <h2 class="flex items-center gap-2 font-bold text-white">
                        <i class="fas fa-cog text-peri"></i> Pengaturan
                    </h2>
                </div>
                <div class="space-y-5 p-6">

                    {{-- Icon --}}
                    <div>
                        <label for="icon" class="mb-1.5 block text-sm font-medium text-gray-300">
                            Icon <span class="text-xs text-gray-500">(class Font Awesome)</span>
                        </label>
                        <input type="text" id="icon" name="icon"
                               value="{{ old('icon', 'fas fa-tag') }}"
                               placeholder="fas fa-gamepad"
                               class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-peri">
                        @error('icon') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Color --}}
                    <div>
                        <label for="color" class="mb-1.5 block text-sm font-medium text-gray-300">Warna</label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="color" name="color"
                                   value="{{ old('color', '#6c63ff') }}"
                                   class="h-10 w-10 cursor-pointer rounded-lg border border-gray-700 bg-gray-800">
                            <input type="text" value="{{ old('color', '#6c63ff') }}"
                                   @input="document.getElementById('color').value = $el.value"
                                   x-init="$watch('$el.value', v => document.getElementById('color').value = v)"
                                   placeholder="#6c63ff"
                                   class="flex-1 rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-peri">
                        </div>
                        @error('color') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Sort Order --}}
                    <div>
                        <label for="sort_order" class="mb-1.5 block text-sm font-medium text-gray-300">Urutan</label>
                        <input type="number" id="sort_order" name="sort_order"
                               value="{{ old('sort_order', 0) }}" min="0"
                               class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-transparent focus:ring-2 focus:ring-peri">
                        @error('sort_order') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Is Active --}}
                    <div class="flex items-center justify-between rounded-xl bg-gray-800 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-300">Status Aktif</p>
                            <p class="text-xs text-gray-500">Tampilkan di halaman depan</p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                   class="peer sr-only">
                            <div class="h-6 w-11 rounded-full bg-gray-700 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-gray-400 after:transition-all peer-checked:bg-peri peer-checked:after:translate-x-full peer-checked:after:bg-white peer-focus:ring-2 peer-focus:ring-peri"></div>
                        </label>
                    </div>

                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 rounded-xl bg-peri px-5 py-3 text-center text-sm font-semibold text-white transition hover:bg-peri-dark">
                    <i class="fas fa-save mr-1"></i> Simpan Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}"
                   class="rounded-xl border border-gray-700 px-5 py-3 text-center text-sm font-medium text-gray-400 transition hover:text-white">
                    Batal
                </a>
            </div>
        </div>

    </div>
</form>
@endsection

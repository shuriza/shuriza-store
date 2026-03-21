@php $isEdit = isset($coupon); @endphp

<div class="rounded-2xl border border-gray-800 bg-gray-900 p-6 space-y-5">

    {{-- Code & Name --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="code" class="mb-1.5 block text-sm font-medium text-gray-300">Kode Kupon <span class="text-red-500">*</span></label>
            <input type="text" name="code" id="code" value="{{ old('code', $isEdit ? $coupon->code : '') }}" required
                   placeholder="DISKON20" style="text-transform:uppercase"
                   class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
            @error('code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-300">Nama Kupon <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $isEdit ? $coupon->name : '') }}" required
                   placeholder="Diskon Ramadhan 20%"
                   class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Type & Value --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="type" class="mb-1.5 block text-sm font-medium text-gray-300">Tipe Diskon <span class="text-red-500">*</span></label>
            <select name="type" id="type"
                    class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
                <option value="fixed" {{ old('type', $isEdit ? $coupon->type : '') === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                <option value="percent" {{ old('type', $isEdit ? $coupon->type : '') === 'percent' ? 'selected' : '' }}>Persentase (%)</option>
            </select>
        </div>
        <div>
            <label for="value" class="mb-1.5 block text-sm font-medium text-gray-300">Nilai Diskon <span class="text-red-500">*</span></label>
            <input type="number" name="value" id="value" value="{{ old('value', $isEdit ? $coupon->value : '') }}" required min="1"
                   placeholder="20000"
                   class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
            @error('value') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Min Order & Max Discount --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="min_order" class="mb-1.5 block text-sm font-medium text-gray-300">Minimum Order (Rp)</label>
            <input type="number" name="min_order" id="min_order" value="{{ old('min_order', $isEdit ? $coupon->min_order : 0) }}" min="0"
                   class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
            <p class="mt-1 text-xs text-gray-500">Isi 0 jika tidak ada minimum.</p>
        </div>
        <div>
            <label for="max_discount" class="mb-1.5 block text-sm font-medium text-gray-300">Maks. Diskon (Rp)</label>
            <input type="number" name="max_discount" id="max_discount" value="{{ old('max_discount', $isEdit ? $coupon->max_discount : '') }}" min="0"
                   placeholder="Kosongkan jika tanpa batas"
                   class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
            <p class="mt-1 text-xs text-gray-500">Hanya untuk tipe persentase.</p>
        </div>
    </div>

    {{-- Usage Limit --}}
    <div>
        <label for="usage_limit" class="mb-1.5 block text-sm font-medium text-gray-300">Batas Penggunaan</label>
        <input type="number" name="usage_limit" id="usage_limit" value="{{ old('usage_limit', $isEdit ? $coupon->usage_limit : '') }}" min="1"
               placeholder="Kosongkan jika tanpa batas"
               class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
    </div>

    {{-- Date Range --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="starts_at" class="mb-1.5 block text-sm font-medium text-gray-300">Mulai Berlaku</label>
            <input type="datetime-local" name="starts_at" id="starts_at"
                   value="{{ old('starts_at', $isEdit && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
                   class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
        </div>
        <div>
            <label for="expires_at" class="mb-1.5 block text-sm font-medium text-gray-300">Berakhir</label>
            <input type="datetime-local" name="expires_at" id="expires_at"
                   value="{{ old('expires_at', $isEdit && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                   class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
            @error('expires_at') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex items-center gap-3 pt-2">
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-peri px-6 py-3 text-sm font-bold text-white shadow-lg shadow-peri/25 transition hover:bg-peri/90">
            <i class="fas fa-save"></i> {{ $isEdit ? 'Perbarui' : 'Simpan' }} Kupon
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="rounded-xl px-4 py-3 text-sm font-medium text-gray-400 hover:text-white transition">
            Batal
        </a>
    </div>
</div>

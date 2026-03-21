@extends('layouts.admin')
@section('title', isset($faq) ? 'Edit FAQ' : 'Tambah FAQ')
@section('page-title', isset($faq) ? 'Edit FAQ' : 'Tambah FAQ')
@section('breadcrumb')
    <a href="{{ route('admin.faqs.index') }}" class="hover:text-white transition">FAQ</a>
    <i class="fas fa-chevron-right text-[0.5rem] mx-1"></i> {{ isset($faq) ? 'Edit' : 'Tambah' }}
@endsection

@section('content')
<form action="{{ isset($faq) ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" method="POST">
    @csrf
    @if(isset($faq)) @method('PUT') @endif

    <div class="max-w-3xl space-y-6">
        <div class="bg-gray-900 rounded-2xl border border-gray-800">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-white font-bold flex items-center gap-2"><i class="fas fa-question-circle text-peri"></i> Detail FAQ</h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label for="question" class="block text-sm font-medium text-gray-300 mb-1.5">Pertanyaan <span class="text-red-400">*</span></label>
                    <input type="text" id="question" name="question" required value="{{ old('question', $faq->question ?? '') }}"
                           placeholder="Contoh: Bagaimana cara melakukan pembelian?"
                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent">
                    @error('question') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="answer" class="block text-sm font-medium text-gray-300 mb-1.5">Jawaban <span class="text-red-400">*</span></label>
                    <textarea id="answer" name="answer" rows="6" required
                              placeholder="Tulis jawaban lengkap di sini..."
                              class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent resize-y">{{ old('answer', $faq->answer ?? '') }}</textarea>
                    @error('answer') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-300 mb-1.5">Urutan</label>
                        <input type="number" id="sort_order" name="sort_order" min="0" value="{{ old('sort_order', $faq->sort_order ?? 0) }}"
                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-peri focus:border-transparent">
                    </div>
                    <div class="flex items-end pb-1">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded bg-gray-800 border-gray-600 text-peri focus:ring-peri focus:ring-offset-0">
                            <span class="text-sm font-medium text-white">Aktif</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.faqs.index') }}" class="flex-1 rounded-xl bg-white/5 py-3 text-center text-sm font-semibold text-gray-300 hover:bg-white/10">Batal</a>
            <button type="submit" class="flex-1 rounded-xl bg-peri py-3 text-center text-sm font-bold text-white hover:bg-peri-dark">
                <i class="fas fa-save mr-1"></i> {{ isset($faq) ? 'Simpan' : 'Tambah FAQ' }}
            </button>
        </div>
    </div>
</form>
@endsection

@props(['product', 'class' => 'w-full h-full'])

@if($product->image_url)
    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
         class="{{ $class }} object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
@else
    @php
        $color = $product->category?->color ?? '#6c63ff';
        $icon = $product->category?->icon ?? 'fas fa-box';
        $catName = $product->category?->name ?? 'Produk';
    @endphp
    <div class="{{ $class }} flex flex-col items-center justify-center relative overflow-hidden"
         style="background: linear-gradient(135deg, {{ $color }}15, {{ $color }}30);">
        {{-- Decorative circles --}}
        <div class="absolute -top-6 -right-6 w-20 h-20 rounded-full opacity-20" style="background: {{ $color }};"></div>
        <div class="absolute -bottom-4 -left-4 w-16 h-16 rounded-full opacity-10" style="background: {{ $color }};"></div>
        {{-- Icon --}}
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-2 shadow-sm"
             style="background: {{ $color }}25;">
            <i class="{{ $icon }} text-2xl" style="color: {{ $color }};"></i>
        </div>
        {{-- Category name --}}
        <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full"
              style="color: {{ $color }}; background: {{ $color }}15;">
            {{ $catName }}
        </span>
    </div>
@endif

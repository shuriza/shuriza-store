@props(['product', 'class' => ''])

@php
    $isWishlisted = auth()->check() && auth()->user()->wishlistedProducts->contains('id', $product->id);
@endphp

<button
    type="button"
    onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist({{ $product->id }}, this)"
    class="wishlist-btn {{ $class }} transition-all duration-200"
    data-product-id="{{ $product->id }}"
    data-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}"
    title="{{ $isWishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
>
    <i class="{{ $isWishlisted ? 'fas' : 'far' }} fa-heart"></i>
</button>

@once
@push('scripts')
<script>
function toggleWishlist(productId, button) {
    @auth
    fetch(`/wishlist/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const icon = button.querySelector('i');
            if (data.added) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                button.dataset.wishlisted = 'true';
                button.title = 'Hapus dari Wishlist';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                button.dataset.wishlisted = 'false';
                button.title = 'Tambah ke Wishlist';
            }
            // Optional: show toast notification
            if (typeof showToast === 'function') {
                showToast(data.message, 'success');
            }
        }
    })
    .catch(err => {
        console.error('Wishlist error:', err);
    });
    @else
    // Redirect to login if not authenticated
    window.location.href = '{{ route("login") }}?redirect=' + encodeURIComponent(window.location.pathname);
    @endauth
}
</script>
@endpush
@endonce

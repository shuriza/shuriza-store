<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // ─── Helper: identifikasi cart (user_id atau session_id) ─────────────────

    private function getCartQuery()
    {
        $query = CartItem::with('product');

        if (Auth::check()) {
            return $query->where('user_id', Auth::id());
        }

        return $query->where('session_id', session()->getId());
    }

    private function cartCondition(array $extra = []): array
    {
        if (Auth::check()) {
            return array_merge(['user_id' => Auth::id()], $extra);
        }

        return array_merge(['session_id' => session()->getId()], $extra);
    }

    // ─── GET /cart ────────────────────────────────────────────────────────────

    public function index()
    {
        $items = $this->getCartQuery()->get();

        $total = $items->sum(fn($item) => $item->quantity * ($item->product?->effective_price ?? 0));

        return view('cart.index', compact('items', 'total'));
    }

    // ─── POST /cart/add ───────────────────────────────────────────────────────

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1|max:99',
        ]);

        $product = Product::active()->findOrFail($request->product_id);

        if (! $product->is_in_stock) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Maaf, stok produk sedang habis.'], 422);
            }
            return back()->with('error', 'Maaf, stok produk sedang habis.');
        }

        $qty = (int) $request->get('quantity', 1);

        $condition = $this->cartCondition(['product_id' => $product->id]);

        $cartItem = CartItem::firstOrNew($condition);

        // Cek batas stok
        $newQty = ($cartItem->exists ? $cartItem->quantity : 0) + $qty;
        if ($product->stock > 0 && $newQty > $product->stock) {
            $newQty = $product->stock;
        }

        $cartItem->fill($condition);
        $cartItem->quantity = $newQty;
        $cartItem->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => "{$product->name} ditambahkan ke keranjang!",
                'cart_count' => $this->getCartCount(),
                'cart_total' => $this->getCartTotal(),
            ]);
        }

        return back()->with('success', "{$product->name} ditambahkan ke keranjang!");
    }

    // ─── PATCH /cart/{id} ─────────────────────────────────────────────────────

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $qty = (int) $request->quantity;

        // Cek stok
        if ($cartItem->product && $cartItem->product->stock > 0 && $qty > $cartItem->product->stock) {
            $qty = $cartItem->product->stock;
        }

        $cartItem->update(['quantity' => $qty]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'subtotal'   => 'Rp ' . number_format($cartItem->subtotal, 0, ',', '.'),
                'cart_count' => $this->getCartCount(),
                'cart_total' => $this->getCartTotal(),
            ]);
        }

        return back()->with('success', 'Keranjang diperbarui.');
    }

    // ─── DELETE /cart/{id} ────────────────────────────────────────────────────

    public function remove(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $name = $cartItem->product?->name ?? 'Produk';
        $cartItem->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => "{$name} dihapus dari keranjang.",
                'cart_count' => $this->getCartCount(),
                'cart_total' => $this->getCartTotal(),
            ]);
        }

        return back()->with('success', "{$name} dihapus dari keranjang.");
    }

    // ─── DELETE /cart ─────────────────────────────────────────────────────────

    public function clear(Request $request)
    {
        $this->getCartQuery()->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Keranjang dikosongkan.',
                'cart_count' => 0,
                'cart_total' => 'Rp 0',
            ]);
        }

        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }

    // ─── GET /cart/count (AJAX) ───────────────────────────────────────────────

    public function count()
    {
        return response()->json([
            'count' => $this->getCartCount(),
            'total' => $this->getCartTotal(),
        ]);
    }

    // ─── GET /cart/items (AJAX — for sidebar) ────────────────────────────────

    public function items()
    {
        $items = $this->getCartQuery()->get()->map(function ($item) {
            $product = $item->product;
            return [
                'id'       => $item->id,
                'quantity' => $item->quantity,
                'subtotal' => $item->quantity * ($product?->effective_price ?? 0),
                'product'  => $product ? [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'slug'  => $product->slug,
                    'price' => $product->effective_price,
                    'formatted_price' => $product->formatted_effective_price,
                    'image_url' => $product->image_url,
                    'stock' => $product->stock,
                ] : null,
            ];
        });

        return response()->json([
            'items' => $items,
            'count' => $this->getCartCount(),
            'total' => $this->getCartTotal(),
        ]);
    }

    // ─── Merge session cart ke user cart setelah login ────────────────────────

    public static function mergeSessionCart(?string $oldSessionId = null): void
    {
        if (! Auth::check()) return;

        $sessionId = $oldSessionId ?? session()->getId();
        $userId    = Auth::id();

        $sessionItems = CartItem::where('session_id', $sessionId)->with('product')->get();

        foreach ($sessionItems as $sessionItem) {
            if (! $sessionItem->product || ! $sessionItem->product->is_active) {
                $sessionItem->delete();
                continue;
            }

            $existing = CartItem::where('user_id', $userId)
                ->where('product_id', $sessionItem->product_id)
                ->first();

            if ($existing) {
                $newQty = $existing->quantity + $sessionItem->quantity;
                if ($sessionItem->product->stock > 0) {
                    $newQty = min($newQty, $sessionItem->product->stock);
                }
                $existing->update(['quantity' => $newQty]);
                $sessionItem->delete();
            } else {
                $sessionItem->update([
                    'user_id'    => $userId,
                    'session_id' => null,
                ]);
            }
        }
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function getCartCount(): int
    {
        return $this->getCartQuery()->sum('quantity');
    }

    private function getCartTotal(): string
    {
        $items = $this->getCartQuery()->get();
        $total = $items->sum(fn($item) => $item->quantity * ($item->product?->effective_price ?? 0));
        return 'Rp ' . number_format($total, 0, ',', '.');
    }

    private function authorizeCartItem(CartItem $cartItem): void
    {
        if (Auth::check()) {
            if ($cartItem->user_id !== Auth::id()) {
                abort(403, 'Akses ditolak.');
            }
        } else {
            if ($cartItem->session_id !== session()->getId()) {
                abort(403, 'Akses ditolak.');
            }
        }
    }
}

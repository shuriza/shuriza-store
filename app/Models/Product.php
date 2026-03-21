<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'original_price',
        'flash_sale_price',
        'flash_sale_start',
        'flash_sale_end',
        'stock',
        'image',
        'badge',
        'is_active',
        'is_popular',
        'sort_order',
        'views',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_popular' => 'boolean',
        'price'      => 'integer',
        'original_price'  => 'integer',
        'flash_sale_price' => 'integer',
        'flash_sale_start' => 'datetime',
        'flash_sale_end'   => 'datetime',
        'stock'      => 'integer',
        'views'      => 'integer',
        'sort_order' => 'integer',
    ];

    // ─────────────────────────────────────────
    // Boot
    // ─────────────────────────────────────────
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && ! $product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::deleting(function ($product) {
            $product->cartItems()->delete();
        });
    }

    // ─────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    // ─────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query, $threshold = null)
    {
        $threshold ??= (int) config('app.low_stock_threshold', 5);
        return $query->where('stock', '<=', $threshold);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeByCategory($query, $categorySlug)
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhere('short_description', 'like', "%{$keyword}%");
        });
    }

    public function scopeSorted($query, $sort = 'default')
    {
        return match ($sort) {
            'price-asc'  => $query->orderBy('price', 'asc'),
            'price-desc' => $query->orderBy('price', 'desc'),
            'name-asc'   => $query->orderBy('name', 'asc'),
            'newest'     => $query->orderBy('created_at', 'desc'),
            'popular'    => $query->orderBy('is_popular', 'desc')->orderBy('views', 'desc'),
            default      => $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc'),
        };
    }

    // ─────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedOriginalPriceAttribute(): ?string
    {
        if (! $this->original_price) return null;
        return 'Rp ' . number_format($this->original_price, 0, ',', '.');
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->original_price || $this->original_price <= $this->price) {
            return null;
        }
        return (int) round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return '';
    }

    public function getBadgeLabelAttribute(): ?string
    {
        return match ($this->badge) {
            'hot'  => 'Hot',
            'sale' => 'Sale',
            'new'  => 'Baru',
            default => null,
        };
    }

    public function getBadgeColorAttribute(): ?string
    {
        return match ($this->badge) {
            'hot'  => 'bg-red-500',
            'sale' => 'bg-yellow-500',
            'new'  => 'bg-green-500',
            default => null,
        };
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ─────────────────────────────────────────
    // Flash Sale
    // ─────────────────────────────────────────
    public function getIsFlashSaleAttribute(): bool
    {
        return $this->flash_sale_price
            && $this->flash_sale_start
            && $this->flash_sale_end
            && now()->between($this->flash_sale_start, $this->flash_sale_end);
    }

    public function getEffectivePriceAttribute(): int
    {
        return $this->is_flash_sale ? $this->flash_sale_price : $this->price;
    }

    public function getFormattedEffectivePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->effective_price, 0, ',', '.');
    }

    public function getFlashSalePercentAttribute(): ?int
    {
        if (!$this->is_flash_sale) return null;
        return (int) round((($this->price - $this->flash_sale_price) / $this->price) * 100);
    }

    public function scopeFlashSale($query)
    {
        return $query->whereNotNull('flash_sale_price')
            ->where('flash_sale_start', '<=', now())
            ->where('flash_sale_end', '>=', now());
    }
}

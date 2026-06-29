<?php

namespace App\Models;

use App\Support\Settings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'brand', 'short_description', 'description',
        'price', 'sale_price', 'show_price', 'sizes', 'colors', 'material',
        'stock_status', 'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'show_price' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sizes' => 'array',
        'colors' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = static::uniqueSlug($product->name, $product->id);
            }
        });
    }

    public static function uniqueSlug(string $name, $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /** Whether price should be visible: global toggle AND per-product flag. */
    public function getPriceVisibleAttribute(): bool
    {
        return Settings::bool('show_prices', true) && (bool) $this->show_price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price > 0 && (float) $this->sale_price < (float) $this->price;
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->is_on_sale ? (float) $this->sale_price : (float) $this->price;
    }

    public function getHasVariantsAttribute(): bool
    {
        return $this->relationLoaded('variants') ? $this->variants->isNotEmpty() : $this->variants()->exists();
    }

    /** Lowest effective price across variants (falls back to the base price). */
    public function getPriceFromAttribute(): float
    {
        if ($this->has_variants) {
            return (float) $this->variants->min(fn ($v) => $v->effective_price);
        }
        return $this->effective_price;
    }

    public function getPriceToAttribute(): float
    {
        if ($this->has_variants) {
            return (float) $this->variants->max(fn ($v) => $v->effective_price);
        }
        return $this->effective_price;
    }

    /** True when variant prices differ, so the storefront shows a "from" price. */
    public function getHasPriceRangeAttribute(): bool
    {
        return $this->has_variants && $this->price_from < $this->price_to;
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->is_on_sale || (float) $this->price <= 0) {
            return null;
        }
        return (int) round((1 - ((float) $this->sale_price / (float) $this->price)) * 100);
    }

    public function getPrimaryImageAttribute(): ?string
    {
        $img = $this->images->first();
        return $img?->url;
    }

    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->approvedReviews()->avg('rating'), 1);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

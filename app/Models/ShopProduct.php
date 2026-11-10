<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProduct extends Model
{
    protected $fillable = ['shop_id', 'product_id', 'price', 'details', 'thumbnail_path'];
    protected $casts = ['price' => 'decimal:2'];
    protected $appends = ['thumbnail_url'];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $path = $this->thumbnail_path ?: $this->product?->thumbnail_path;
        return $path ? asset('storage/' . $path) : null;
    }
}

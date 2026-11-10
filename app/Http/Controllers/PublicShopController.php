<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Inertia\Inertia;

class PublicShopController extends Controller
{
    public function index()
    {
        return Inertia::render('Public/Shops',
            ['shops' => Shop::withCount('listings')->orderBy('name')->get()]);
    }

    public function show(Shop $shop)
    {
        return Inertia::render('Public/Shop', ['shop' => $shop->only('id', 'name', 'slug', 'description'),
            'listings' => $shop->listings()->with('product')->latest()->get()->map(fn($listing) => ['id' => $listing->id, 'name' => $listing->product->name, 'price' => $listing->price, 'details'
            => $listing->details ?? $listing->product->description, 'thumbnail_url'
            => $listing->thumbnail_url])]);
    }
}

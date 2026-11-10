<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OwnerController extends Controller
{
    private function shop(Request $request): ?Shop
    {
        return $request->user()->shop;
    }

    public function dashboard(Request $request)
    {
        $shop = $this->shop($request);
        return Inertia::render('Owner/Dashboard', ['shop' => $shop, 'listingCount' => $shop?->listings()->count() ?? 0, 'catalogCount' => Product::count()]);
    }

    public function catalog(Request $request)
    {
        $shop = $this->shop($request);
        return Inertia::render('Owner/Catalog', ['shop' => $shop, 'products' => Product::with('creator:id,name')->orderBy('name')->get(), 'listedIds' => $shop ? $shop->listings()->pluck('product_id')->all() : []]);
    }

    public function listings(Request $request)
    {
        $shop = $this->shop($request);
        return Inertia::render('Owner/Listings', ['shop' => $shop, 'listings' => $shop ? $shop->listings()->with('product')->latest()->get() : []]);
    }

    public function attach(Request $request, Product $product)
    {
        $shop = $this->shop($request);
        abort_unless($shop, 403, 'No shop assigned to this account.');
        $data = $request->validate(['price' => 'required|numeric|min:0|max:9999999999.99', 'details' => 'nullable|string|max:5000', 'thumbnail' => 'nullable|image|max:2048']);
        if ($shop->listings()->where('product_id', $product->id)->exists()) return back()->withErrors(['product' => 'Product is already listed.']);
        $data['shop_id'] = $shop->id;
        $data['product_id'] = $product->id;
        if ($request->hasFile('thumbnail')) $data['thumbnail_path'] = $request->file('thumbnail')->store('shop-products', 'public');
        unset($data['thumbnail']);
        ShopProduct::create($data);
        return back()->with('success', 'Product added to your shop.');
    }

    public function updateListing(Request $request, ShopProduct $listing)
    {
        $shop = $this->shop($request);
        abort_unless($shop && $listing->shop_id === $shop->id, 403);
        $data = $request->validate(['price' => 'required|numeric|min:0|max:9999999999.99', 'details' => 'nullable|string|max:5000', 'thumbnail' => 'nullable|image|max:2048']);
        if ($request->hasFile('thumbnail')) {
            $new = $request->file('thumbnail')->store('shop-products', 'public');
            if ($listing->thumbnail_path) Storage::disk('public')->delete($listing->thumbnail_path);
            $data['thumbnail_path'] = $new;
        }
        unset($data['thumbnail']);
        $listing->update($data);
        return back()->with('success', 'Shop listing updated.');
    }

    public function detach(Request $request, ShopProduct $listing)
    {
        $shop = $this->shop($request);
        abort_unless($shop && $listing->shop_id === $shop->id, 403);
        if ($listing->thumbnail_path) Storage::disk('public')->delete($listing->thumbnail_path);
        $listing->delete();
        return back()->with('success', 'Product removed from your shop.');
    }

    public function createProduct(Request $request)
    {
        $shop = $this->shop($request);
        abort_unless($shop, 403, 'No shop assigned to this account.');
        $data = $request->validate(['name' => 'required|string|max:255', 'sku' => 'nullable|string|max:100|unique:products,sku', 'description' => 'nullable|string|max:5000', 'thumbnail' => 'nullable|image|max:2048', 'price' => 'required|numeric|min:0|max:9999999999.99', 'details' => 'nullable|string|max:5000']);
        DB::transaction(function () use ($request, $shop, $data) {
            $path = $request->hasFile('thumbnail') ? $request->file('thumbnail')->store('products', 'public') : null;
            $product = Product::create(['name' => $data['name'], 'sku' => $data['sku'] ?? null, 'description' => $data['description'] ?? null, 'thumbnail_path' => $path, 'created_by' => $request->user()->id]);
            ShopProduct::create(['shop_id' => $shop->id, 'product_id' => $product->id, 'price' => $data['price'], 'details' => $data['details'] ?? null]);
        });
        return redirect()->route('owner.listings')->with('success', 'Product created in shared catalog and added to your shop.');
    }
}

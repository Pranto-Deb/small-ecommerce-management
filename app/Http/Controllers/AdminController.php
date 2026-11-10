<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Product;
use App\Models\User;
use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', ['stats' => ['shops' => Shop::count(), 'products' => Product::count(), 'owners' => User::where('role', 'owner')->count(), 'listings' => ShopProduct::count()]]);
    }

    public function owners()
    {
        return Inertia::render('Admin/Owners', ['owners' => User::where('role', 'owner')->with('shop:id,name,owner_id')->latest()->get()]);
    }

    public function storeOwner(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'email' => 'required|email|max:255|unique:users,email', 'password' => 'required|string|min:8']);
        User::create($data + ['role' => 'owner']);
        return back()->with('success', 'Shop owner created.');
    }

    public function updateOwner(Request $request, User $owner)
    {
        abort_unless($owner->role === 'owner', 404);
        $data = $request->validate(['name' => 'required|string|max:255', 'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($owner->id)], 'password' => 'nullable|string|min:8']);
        if (empty($data['password'])) unset($data['password']);
        $owner->update($data);
        return back()->with('success', 'Shop owner updated.');
    }

    public function destroyOwner(User $owner)
    {
        abort_unless($owner->role === 'owner', 404);
        $owner->shop()->update(['owner_id' => null]);
        $owner->delete();
        return back()->with('success', 'Shop owner deleted.');
    }

    public function shops()
    {
        return Inertia::render('Admin/Shops', ['shops' => Shop::with('owner:id,name')->withCount('listings')->latest()->get(), 'owners' => User::where('role', 'owner')->orderBy('name')->get(['id', 'name', 'email', 'role', 'id'])]);
    }

    public function storeShop(Request $request)
    {
        $data = $this->shopData($request);
        Shop::create($data);
        return back()->with('success', 'Shop created.');
    }

    public function updateShop(Request $request, Shop $shop)
    {
        $data = $this->shopData($request, $shop);
        $shop->update($data);
        return back()->with('success', 'Shop updated.');
    }

    private function shopData(Request $request, ?Shop $shop = null): array
    {
        return $request->validate(['name' => 'required|string|max:255', 'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('shops')->ignore($shop?->id)], 'description' => 'nullable|string|max:5000', 'owner_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'owner'), Rule::unique('shops', 'owner_id')->ignore($shop?->id)]]);
    }

    public function destroyShop(Shop $shop)
    {
        $shop->delete();
        return back()->with('success', 'Shop deleted.');
    }

    public function products()
    {
        return Inertia::render('Admin/Products', ['products' => Product::with('creator:id,name')->withCount('listings')->latest()->get()]);
    }

    public function storeProduct(Request $request)
    {
        $data = $this->productData($request);
        if ($request->hasFile('thumbnail')) $data['thumbnail_path'] = $request->file('thumbnail')->store('products', 'public');
        $data['created_by'] = $request->user()->id;
        Product::create($data);
        return back()->with('success', 'Catalog product created.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $data = $this->productData($request, $product);
        if ($request->hasFile('thumbnail')) {
            $new = $request->file('thumbnail')->store('products', 'public');
            if ($product->thumbnail_path) Storage::disk('public')->delete($product->thumbnail_path);
            $data['thumbnail_path'] = $new;
        }
        $product->update($data);
        return back()->with('success', 'Catalog product updated.');
    }

    private function productData(Request $request, ?Product $product = null): array
    {
        return $request->validate(['name' => 'required|string|max:255', 'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product?->id)], 'description' => 'nullable|string|max:5000', 'thumbnail' => 'nullable|image|max:2048']);
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Product removed from catalog and shops.');
    }
}

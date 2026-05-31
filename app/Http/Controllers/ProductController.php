<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Inisialisasi Query dengan relasi kategori
        $query = Product::with('category');

        // 2. Logika Search
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // 3. Ambil data produk (Urutkan berdasarkan id_product sendiri)
        $products = $query->orderBy('id_product', 'desc')->get();

        // 4. Ambil semua kategori untuk modal (Urutkan berdasarkan id_categories)
        $categories = Category::orderBy('name', 'asc')->get();

        return view('products.products', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required',
            'id_categories' => 'required',
            'gambar_produk' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $data = $request->all();
        $category = Category::find($request->id_categories);
        if ($category) {
            $data['kategori_produk'] = $category->name;
        }

        if ($request->hasFile('gambar_produk')) {
            $data['gambar_produk'] = $request->file('gambar_produk')->store('products', 'public');
        }

        Product::create($data);
        return back()->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->all();

        $category = Category::find($request->id_categories);
        if ($category) {
            $data['kategori_produk'] = $category->name; 
        }

        if ($request->hasFile('gambar_produk')) {
            if ($product->gambar_produk) {
                \Storage::disk('public')->delete($product->gambar_produk);
            }
            $data['gambar_produk'] = $request->file('gambar_produk')->store('products', 'public');
        }

        $product->update($data);

        return back()->with('success', 'Produk berhasil diperbarui!');
    }

    public function show($id)
    {
        return redirect()->route('products.index');
    }
    
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->gambar_produk) {
            Storage::disk('public')->delete($product->gambar_produk);
        }
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus!');
    }
}
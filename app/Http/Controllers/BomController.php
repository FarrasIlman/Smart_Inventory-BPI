<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Bom;
use Illuminate\Http\Request;

class BomController extends Controller
{
    public function index()
    {
        $products = Product::with('boms.rawMaterial')->get();
        $materials = RawMaterial::all();
        
        return view('bom.bom', compact('products', 'materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_product' => 'required',
            'id_bahanbaku' => 'required',
            'jumlah_kebutuhan' => 'required|numeric',
            'persentase_waste' => 'required|numeric',
        ]);

        $save = Bom::create([
            'id_product'       => $request->id_product,
            'id_bahanbaku'    => $request->id_bahanbaku,
            'jumlah_kebutuhan' => $request->jumlah_kebutuhan,
            'persentase_waste' => $request->persentase_waste,
        ]);

        if($save) {
            return back()->with('success', 'Bahan baku berhasil ditambahkan!');
        } else {
            return back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function show($id)
    {
        return redirect()->route('bom.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah_kebutuhan' => 'required|numeric',
            'persentase_waste' => 'required|numeric',
        ]);

        $bom = Bom::findOrFail($id);
        $bom->update([
            'jumlah_kebutuhan' => $request->jumlah_kebutuhan,
            'persentase_waste' => $request->persentase_waste,
        ]);

        return back()->with('success', 'Data resep berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Bom::findOrFail($id)->delete();
        return back()->with('success', 'Bahan berhasil dihapus dari resep!');
    }
}
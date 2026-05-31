<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = RawMaterial::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_bahanbaku', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'kurang') {
                $query->whereRaw('stok <= stok_minimum');
            } elseif ($request->status == 'aman') {
                $query->whereRaw('stok > stok_minimum');
            }
        }

        $items = $query->orderBy('nama_bahanbaku', 'asc')->get();

        return view('items.stok', compact('items'));
    }

    public function store(Request $request) 
    {
        $request->validate([
            'nama_bahanbaku' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'stok' => 'required|numeric',
            'stok_minimum' => 'required|numeric',
            'gambar_bahan' => 'nullable|image|mimes:jpg,png,jpeg|max:5000'
        ]);

        $data = $request->all();
        $data['stok_terkunci'] = 0; 

        if ($request->hasFile('gambar_bahan')) {
            $data['gambar_bahan'] = $request->file('gambar_bahan')->store('bahan_baku', 'public');
        }

        \App\Models\RawMaterial::create($data);
        return redirect()->back()->with('success', 'Bahan baku berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $item = RawMaterial::findOrFail($id);
        
        $request->validate([
            'nama_bahanbaku' => 'required|string|max:255',
            'satuan' => 'required|string',
            'stok' => 'required|numeric',
            'stok_minimum' => 'required|numeric',
            'gambar_bahan' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $oldStock = $item->stok;
        $newStock = $request->stok;

        $data = $request->only(['nama_bahanbaku', 'satuan', 'stok', 'stok_minimum', 'harga']);

        if ($request->hasFile('gambar_bahan')) {
            if ($item->gambar_bahan && Storage::disk('public')->exists($item->gambar_bahan)) {
                Storage::disk('public')->delete($item->gambar_bahan);
            }
            $data['gambar_bahan'] = $request->file('gambar_bahan')->store('bahan_baku', 'public');
        }

        DB::beginTransaction();
        try {
            // Jalankan Update
            $item->update($data);

            // Cek apakah ada perubahan stok fisik
            if ($oldStock != $newStock) {
                $selisih = $newStock - $oldStock;
                
                // Catat ke tabel StockMovement
                \App\Models\StockMovement::create([
                    'id_bahanbaku' => $item->id_bahanbaku,
                    'tipe_transaksi' => 'penyesuaian',
                    'jumlah' => abs($selisih),
                    'tanggal' => now(),
                    'keterangan' => "Penyesuaian stok manual (Dari $oldStock ke $newStock)"
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data bahan baku diperbarui dan riwayat dicatat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function restock(Request $request, $id) 
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'price_per_unit' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        $item = RawMaterial::findOrFail($id);

        DB::beginTransaction();
        try {
            $currentStock = (float)$item->stok;
            $currentPrice = (float)($item->harga ?? 0);
            $newAmount = (float)$request->amount;
            $purchasePrice = (float)$request->price_per_unit;

            $totalStock = $currentStock + $newAmount;

            if ($totalStock > 0) {
                $newAveragePrice = (($currentStock * $currentPrice) + ($newAmount * $purchasePrice)) / $totalStock;
            } else {
                $newAveragePrice = $purchasePrice;
            }

            $item->stok = $totalStock;
            $item->harga = $newAveragePrice;
            $item->save();

            \App\Models\StockMovement::create([
                'id_bahanbaku' => $item->id_bahanbaku,
                'tipe_transaksi' => 'masuk',
                'jumlah' => $newAmount,
                'tanggal' => now(),
                'keterangan' => $request->note ?? "Restock barang: Rp " . number_format($purchasePrice, 0, ',', '.')
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Stok berhasil ditambah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
{
    $item = RawMaterial::findOrFail($id);

    $adaProduksi = \DB::table('production_materials')->where('id_bahanbaku', $id)->exists();
    $adaPembelian = \DB::table('purchase_details')->where('id_bahanbaku', $id)->exists();
    $adaMovement = \DB::table('stock_movements')->where('id_bahanbaku', $id)->exists();

    if ($adaProduksi || $adaPembelian || $adaMovement) {
        $pesan = "Bahan tidak bisa dihapus karena sudah memiliki riwayat ";
        if ($adaProduksi) $pesan .= "produksi ";
        if ($adaPembelian) $pesan .= "pembelian ";
        if ($adaMovement) $pesan .= "stok";

        return redirect()->back()->with('error', $pesan . "!");
    }

    try {
        if ($item->gambar_bahan && \Storage::disk('public')->exists($item->gambar_bahan)) {
            \Storage::disk('public')->delete($item->gambar_bahan);
        }
        
        $item->delete();
        return redirect()->back()->with('success', 'Bahan baku berhasil dihapus!');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menghapus: Data masih terikat dengan transaksi lain.');
    }
}
}
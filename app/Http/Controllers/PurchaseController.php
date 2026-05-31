<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier');
        if ($request->search) {
            $query->where('id_purchase', $request->search);
        }
        if ($request->status) {
            $query->where('status_pembelian', $request->status);
        }

        $purchases = $query->latest()->get();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $materials = RawMaterial::all();
        $suppliers = Supplier::all();

        return view('purchases.create', compact('materials','suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'materials'   => 'required|array|min:1',
            'materials.*' => 'required|exists:raw_materials,id_bahanbaku',
            'jumlah'      => 'required|array|min:1',
            'jumlah.*'    => 'required|numeric|min:0',
            'harga'       => 'required|array|min:1',
            'harga.*'     => 'required|numeric|min:0',
        ],[
            'id_supplier.required' => 'Supplier wajib dipilih',
            'materials.*.required' => 'Semua bahan harus dipilih',
            'jumlah.*.required'    => 'Qty tidak boleh kosong',
            'jumlah.*.min'         => 'Qty minimal 1',
            'harga.*.required'     => 'Harga wajib diisi',
            'harga.*.min'          => 'Harga tidak boleh 0',
        ]);

        DB::beginTransaction();

        try {
            $purchase = Purchase::create([
                'id_supplier' => $request->id_supplier,
                'tanggal_pembelian' => now(),
                'status_pembelian' => 'dipesan'
            ]);

            foreach ($request->materials as $i => $material_id) {

                // ambil data
                $qty   = (float) $request->jumlah[$i];
                $harga = (float) $request->harga[$i];

                // skip kalau kosong
                if (!$material_id || $qty <= 0) {
                    continue;
                }

                // hitung subtotal
                $subtotal = $qty * $harga;

                PurchaseDetail::create([
                    'id_purchase'   => $purchase->id_purchase,
                    'id_bahanbaku' => $material_id,
                    'jumlah'        => $qty,
                    'harga'         => $harga,
                    'subtotal'      => $subtotal
                ]);
            }

            DB::commit();

            return redirect()
                ->route('purchases.index')
                ->with('success', 'Pembelian berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // TAMPILKAN ERROR KE LAYAR
            return response()->json([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
        }
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'id_purchase');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function show($id)
    {
        $purchase = \App\Models\Purchase::with(['supplier','details.material'])
            ->findOrFail($id);

        return view('purchases.show', compact('purchase'));
    }

    public function updateStatus(Request $request, $id) {
        $purchase = Purchase::with('details.rawMaterial')->findOrFail($id);
        if ($request->status_pembelian == 'diterima' && $purchase->status_pembelian != 'diterima') {
            $this->prosesStokMasuk($purchase);
        }
        $purchase->update(['status_pembelian' => $request->status_pembelian]);
        return back()->with('success','Status diperbarui');
    }

    public function edit($id)
    {
        $purchase  = \App\Models\Purchase::with('details.material')->findOrFail($id);
        $materials = \App\Models\RawMaterial::all();
        $suppliers = \App\Models\Supplier::all();

        return view('purchases.edit', compact('purchase','materials','suppliers'));
    }

    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        DB::beginTransaction();

        try {
            $statusLama = $purchase->status_pembelian;

            $purchase->update([
                'id_supplier' => $request->id_supplier,
                'status_pembelian' => $request->status_pembelian
            ]);

            
            if ($request->status_pembelian == 'diterima' && $statusLama != 'diterima') {
                $this->prosesStokMasuk($purchase);
            }

            // hapus detail lama
            $purchase->details()->delete();

            $total = 0;

            foreach ($request->materials as $i => $material_id) {

                $qty   = (float) $request->jumlah[$i];
                $harga = (float) $request->harga[$i];

                if (!$material_id || $qty <= 0) {
                    continue;
                }

                $subtotal = $qty * $harga;
                $total += $subtotal;

                PurchaseDetail::create([
                    'id_purchase'   => $purchase->id_purchase,
                    'id_bahanbaku' => $material_id,
                    'jumlah'        => $qty,
                    'harga'         => $harga,
                    'subtotal'      => $subtotal
                ]);
            }

            // update total
            $purchase->update([
                'total' => $total
            ]);

            DB::commit();

            return redirect()
                ->route('purchases.show', $purchase->id_purchase)
                ->with('success', 'Pembelian berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function receive($id)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::with('details.rawMaterial')->findOrFail($id);

            if ($purchase->status_pembelian === 'diterima') {
                return back()->with('error', 'Pembelian ini sudah berstatus diterima.');
            }

            $this->prosesStokMasuk($purchase);

            $purchase->update([
                'status_pembelian' => 'diterima'
            ]);

            DB::commit();
            return back()->with('success', 'Barang diterima dan riwayat mutasi tercatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $purchase = \App\Models\Purchase::findOrFail($id);

        if ($purchase->status_pembelian === 'diterima') {
            return back()->with('error', 'Pembelian sudah diterima, tidak bisa dihapus');
        }

        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Pembelian berhasil dihapus');
    }

    private function prosesStokMasuk($purchase)
    {
        foreach ($purchase->details as $detail) {
            $material = $detail->rawMaterial;
            if (!$material) continue;

            $currentStock = $material->stok;
            $currentPrice = $material->harga ?? 0;
            $incomingQty = $detail->jumlah;
            $incomingPrice = $detail->harga;

            $totalStockAfter = $currentStock + $incomingQty;
            
            // Hitung Harga Rata-Rata
            $newAveragePrice = ($totalStockAfter > 0) 
                ? (($currentStock * $currentPrice) + ($incomingQty * $incomingPrice)) / $totalStockAfter 
                : $incomingPrice;

            // 1. Update Tabel Bahan Baku
            $material->update([
                'stok' => $totalStockAfter,
                'harga' => $newAveragePrice
            ]);

            // 2. Simpan ke Tabel Stock Movement
            \App\Models\StockMovement::create([
                'id_bahanbaku'   => $material->id_bahanbaku,
                'tipe_transaksi' => 'masuk',
                'jumlah'         => $incomingQty,
                'tanggal'        => now(),
                'keterangan'     => "Masuk dari Pembelian #" . $purchase->id_purchase
            ]);
        }
    }
}
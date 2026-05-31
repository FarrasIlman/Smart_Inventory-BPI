<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\RawMaterial;
use App\Models\Production;
use App\Models\ProductionMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['product', 'details']);

        // Fitur Search
        if ($request->filled('search')) {
            $query->where('nama_pelanggan', 'like', '%' . $request->search . '%');
        }

        // Fitur Filter Status
        if ($request->filled('status')) {
            $query->where('status_order', $request->status);
        }

        // Sort
        $orders = $query
        ->orderByRaw("CASE 
            WHEN status_order = 'menunggu bahan' THEN 1
            WHEN status_order = 'siap produksi' THEN 2 
            WHEN status_order = 'produksi' THEN 3
            WHEN status_order = 'selesai' THEN 4 
            WHEN status_order = 'dibatalkan' THEN 5
            ELSE 6 
        END ASC")
        ->orderBy('deadline', 'asc')
        ->paginate(10);
        
        return view('orders.orders', compact('orders'));
    }

    public function checkMaterials($id)
    {
        $order = Order::with(['product.boms.rawMaterial', 'production.materials'])->findOrFail($id);
        
        $results = [];
        foreach ($order->product->boms as $bom) {
            $material = $bom->rawMaterial;
            
            $needed = $bom->jumlah_kebutuhan * $order->jumlah_pesanan;
            
            $stockPhysical = $material->stok;
            $stockLockedTotal = $material->stok_terkunci;

            // 2. Cek berapa banyak bahan ini yang SUDAH dikunci oleh pesanan ini
            $alreadyLockedByThisOrder = 0;
            if ($order->production) {
                $productionMaterial = $order->production->materials
                    ->where('id_bahanbaku', $material->id_bahanbaku)
                    ->first();
                
                $alreadyLockedByThisOrder = $productionMaterial ? $productionMaterial->jumlah_estimasi : 0;
            }

            // 3. HITUNG KETERSEDIAAN MURNI:
            $lockedByOthers = $stockLockedTotal - $alreadyLockedByThisOrder;
            $stockAvailableForDisplay = $stockPhysical - $lockedByOthers;

            // 4. Tentukan kekurangan berdasarkan ketersediaan tampilan
            $shortage = ($needed > $stockAvailableForDisplay) ? ($needed - $stockAvailableForDisplay) : 0;

            $results[] = [
                'nama_bahanbaku' => $material->nama_bahanbaku, 
                'satuan'          => $material->satuan,
                'butuh'           => $needed,
                'stok_gudang'     => $stockPhysical,
                'stok_terkunci'   => $stockLockedTotal,
                'ketersediaan'    => $stockAvailableForDisplay,
                'kekurangan'      => $shortage,
                'status'          => ($needed <= $stockAvailableForDisplay) ? 'CUKUP' : 'KURANG'
            ];
        }

        return view('orders.check', compact('order', 'results'));
    }

    public function show($id)
    {
        $order = Order::with(['product', 'details'])->findOrFail($id);   

        $production = Production::with('materials.material')
            ->where('id_order', $order->id_order)
            ->first();

        return view('orders.show', compact('order','production'));
    }

    public function edit($id)
    {
        $order = Order::with('details')->findOrFail($id);
        $products = Product::all(); 
        return view('orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'id_product'     => 'required|exists:products,id_product',
            'deadline'       => 'required|date',
            'status_order'   => 'required|in:menunggu bahan,siap produksi,produksi,perlu dikirim,dikirim,   
                                selesai',
            'gambar_desain'  => 'nullable|image|mimes:jpg,png,jpeg|max:10240',
            'sizes'          => 'required|array|min:1',
            'quantities'     => 'required|array|min:1',
            'harga_satuan'   => 'required|numeric|min:0',
        ], [
            'sizes.required' => 'Wajib menambahkan minimal satu rincian ukuran.',
        ]);

        $order = Order::findOrFail($id);

        // 1. HITUNG ULANG JUMLAH PESANAN BERDASARKAN RINCIAN UKURAN
        $realTotalQty = array_sum($request->quantities);

        $dataMain = $request->except(['sizes', 'quantities', 'gambar_desain']);
        
        $harga = (int) str_replace(['.', ','], '', $request->harga_satuan);
        
        $dataMain['harga_satuan'] = $harga;
        $dataMain['jumlah_pesanan'] = $realTotalQty; 
        $dataMain['total_harga']   = $realTotalQty * $harga;

        $dataMain['alamat'] = $request->alamat;
        $dataMain['no_telepon'] = $request->no_telepon;

        if ($request->hasFile('gambar_desain')) {
            if ($order->gambar_desain) {
                Storage::disk('public')->delete($order->gambar_desain);
            }
            $dataMain['gambar_desain'] = $request->file('gambar_desain')->store('designs', 'public');
        }

        // Update main order
        $order->update($dataMain);

        // === HANDLE PRODUCTION ===
        if (in_array($request->status_order, ['siap produksi', 'produksi'])) {
            $production = Production::firstOrCreate(
                ['id_order' => $order->id_order],
                [
                    'tanggal_mulai' => now(),
                    'jumlah_produksi' => $realTotalQty
                ]
            );

            // Jika jumlah pesanan berubah, update juga target di tabel produksi
            $production->update(['jumlah_produksi' => $realTotalQty]);

            // RE-CALCULATE ESTIMASI BAHAN
            // Jika bahan sudah ada, update jumlah_estimasinya karena Qty pesanan berubah
            if ($production->materials()->count() > 0) {
                $order->load('product.boms');
                foreach ($order->product->boms as $bom) {
                    ProductionMaterial::where('id_production', $production->id_production)
                        ->where('id_bahanbaku', $bom->id_bahanbaku)
                        ->update([
                            'jumlah_estimasi' => $bom->jumlah_kebutuhan * $realTotalQty
                        ]);
                }
            } else {
                // Jika belum ada, buat baru
                $order->load('product.boms');
                foreach ($order->product->boms as $bom) {
                    ProductionMaterial::create([
                        'id_production'   => $production->id_production,
                        'id_bahanbaku'    => $bom->id_bahanbaku,
                        'jumlah_estimasi' => $bom->jumlah_kebutuhan * $realTotalQty
                    ]);
                }
            }
        }

        // Update tanggal selesai jika status Selesai
        if ($request->status_order === 'selesai') {
            Production::where('id_order', $order->id_order)->update(['tanggal_selesai' => now()]);
            $order->update(['tahap_produksi' => 'selesai']);
        }

        // === UPDATE SIZE DETAIL ===
        $order->details()->delete();
        foreach ($request->sizes as $key => $size) {
            $qty = $request->quantities[$key];
            if (!empty($size) && $qty > 0) {
                OrderDetail::create([
                    'id_order' => $order->id_order,
                    'size'     => $size,
                    'quantity' => $qty
                ]);
            }
        }

        return redirect()
            ->route('orders.show', $order->id_order) 
            ->with('success', 'Pesanan #' . $order->id_order . ' berhasil diperbarui dan disinkronkan!');
    }
    
    public function create()
    {
        $products = Product::all();
        return view('orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required',
            'id_product' => 'required',
            'jumlah_pesanan' => 'required|numeric|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'deadline' => 'required|date',
        ]);

        $data = $request->except(['sizes', 'quantities','gambar_desain']);
        $data['tanggal_pesan'] = now();

        $harga = str_replace(['.', ','], '', $request->harga_satuan);
        $qty = (int) $request->jumlah_pesanan;

        $data['harga_satuan'] = $harga;

        $data['total_harga'] = bcmul(
            (string) $qty,
            (string) $harga,
            2
        );

        $data['alamat'] = $request->alamat;
        $data['no_telepon'] = $request->no_telepon;

        // Simpan Gambar jika ada
        if ($request->hasFile('gambar_desain')) {
            $data['gambar_desain'] = $request->file('gambar_desain')->store('designs', 'public');
        }

        // 1. Simpan Order Utama
        $order = Order::create($data);

        // 2. Simpan Rincian Size
        foreach ($request->sizes as $key => $size) {
            $qty = $request->quantities[$key];
            if (!empty($size) && $qty > 0) {
                OrderDetail::create([
                    'id_order' => $order->id_order,
                    'size' => $size,
                    'quantity' => $qty
                ]);
            }
        }

        return redirect()->route('orders.index')->with('success', 'Pesanan baru berhasil ditambahkan!');
    }
    
    public function startProduction($id, $deduct)
    {
        $order = Order::with('product.boms.rawMaterial')->findOrFail($id);

        DB::beginTransaction();

        try {
            // 1. Logika firstOrCreate
            $production = Production::firstOrCreate(
                ['id_order' => $order->id_order],
                [
                    'tanggal_mulai' => now(),
                    'jumlah_produksi' => $order->jumlah_pesanan
                ]
            );

            // 2. Logika pembuatan estimasi bahan
            if ($production->materials()->count() == 0) {
                foreach ($order->product->boms as $bom) {
                    ProductionMaterial::create([
                        'id_production' => $production->id_production,
                        'id_bahanbaku' => $bom->id_bahanbaku,
                        'jumlah_estimasi' => $bom->jumlah_kebutuhan * $order->jumlah_pesanan
                    ]);
                }
            }

            // 3. Stock lock
            if ($deduct === 'yes') {
                foreach ($order->product->boms as $bom) {
                    $totalNeeded = $bom->jumlah_kebutuhan * $order->jumlah_pesanan;
                    $material = $bom->rawMaterial;

                    /* 
                     * Stok Tersedia = Stok Fisik - Stok Terkunci
                     */
                    $availableStock = $material->stok - $material->stok_terkunci;

                    if ($availableStock < $totalNeeded) {
                        throw new \Exception('Stok tersedia tidak cukup: ' . $material->nama_bahanbaku . ' (Sisa: ' . $availableStock . ' ' . $material->satuan . ')');
                    }

                    $material->increment('stok_terkunci', $totalNeeded);
                }
            }

            // 4. Update status
            $order->update([
                'status_order' => 'produksi',
                'tahap_produksi' => 'potong'
            ]);

            DB::commit();

            return back()->with('success', 'Produksi berhasil dimulai dan stok bahan telah dikunci!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function finishProduction(Request $request, $id_production)
{
    $production = Production::with('materials.rawMaterial', 'order')->findOrFail($id_production);

    DB::beginTransaction();
    try {
        foreach ($production->materials as $pm) {
            $material = $pm->rawMaterial;
            
            // Ambil input. Jika kosong/null, fallback ke jumlah_estimasi
            $realization = $request->input('realization_' . $pm->id_bahanbaku);
            if (empty($realization)) {
                $realization = $pm->jumlah_estimasi;
            }

            // 1. Ambil Harga Rata-rata
            $priceAtFinish = $material->harga ?? 0; 
            $totalBiaya = (float)$realization * (float)$priceAtFinish;

            // 2. UPDATE TABEL production_materials
            $pm->update([
                'jumlah_realisasi' => $realization,
                'harga'            => $priceAtFinish,
                'subtotal'         => $totalBiaya
            ]);

            // 3. POTONG STOK FISIK & BUANG KUNCIAN
            $newStok = $material->stok - $realization;
            $newTerkunci = $material->stok_terkunci - $pm->jumlah_estimasi;

            // Jika hasil minus, jadikan 0
            $material->update([
                'stok' => ($newStok < 0) ? 0 : $newStok,
                'stok_terkunci' => ($newTerkunci < 0) ? 0 : $newTerkunci
            ]);

            // 4. CATAT RIWAYAT KELUAR (Stock Movement)
            \App\Models\StockMovement::create([
                'id_bahanbaku'   => $pm->id_bahanbaku,
                'tipe_transaksi' => 'keluar',
                'jumlah'         => $realization,
                'tanggal'        => now(),
                'keterangan'     => "Produksi Selesai: " . ($production->order->nama_pelanggan ?? 'Order #'.$production->id_order)
            ]);
        }

        // 5. UPDATE STATUS ORDER JADI SELESAI
        if ($production->order) {
            $production->order->update(['status_order' => 'perlu dikirim',
            'tahap_produksi' => 'selesai'
            ]);
            
        }

        DB::commit();
        // Redirect ke detail order
        return redirect()->route('orders.show', $production->id_order)
                         ->with('success', 'Produksi Selesai! Stok dipotong & modal tercatat.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    }
}

    public function updateMaterialUsage(Request $request, $id_production)
    {
        $request->validate([
            'materials' => 'required|array',
            'qty' => 'required|array'
        ]);

        DB::beginTransaction();

        try {
            $production = Production::with('materials.material')->findOrFail($id_production);

            foreach ($request->materials as $i => $material_id) {

                $qty = (float) $request->qty[$i];

                $row = ProductionMaterial::where('id_production', $id_production)
                    ->where('id_bahanbaku', $material_id)
                    ->first();

                $material = RawMaterial::findOrFail($material_id);

                if ($material->stok < $qty) {
                    return back()->with('error', 'Stok tidak cukup: ' . $material->nama_bahanbaku);
                }

                $harga = $material->harga ?? 0;
                $subtotal = $qty * $harga;

                $row->update([
                    'jumlah_realisasi' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal
                ]);

                $material->decrement('stok', $qty);
            }

            DB::commit();
            return back()->with('success', 'Pemakaian bahan berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function productionIndex(Request $request)
    {
        $query = Order::query()->with([
            'product', 
            'production.materials.rawMaterial'
        ]);

        // Filter Search (Nama Pelanggan)
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_pelanggan', 'like', '%' . $request->search . '%');
        }

        // Filter Status
        if ($request->has('status') && $request->status != '') {
            $query->where('status_order', $request->status);
        } else {
            $query->whereIn('status_order', ['siap produksi', 'produksi','perlu dikirim','dikirim', 'selesai']);
        }
        // Sort Status
        $orders = $query->orderByRaw("CASE 
            WHEN status_order = 'produksi' THEN 1 
            WHEN status_order = 'siap produksi' THEN 2 
            WHEN status_order = 'selesai' THEN 3 
            ELSE 4 
        END ASC")
        ->orderBy('deadline', 'asc')
        ->get();

        return view('production.production', compact('orders'));
    }

    public function updateStage(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $order->update([
            'tahap_produksi' => $request->tahap
        ]);

        \App\Models\Production::where('id_order', $order->id_order)
            ->update([
                'updated_at' => now()
            ]);

        return back()->with('success', 'Tahap produksi pesanan #' . $order->id_order . ' berhasil diperbarui!');
    }
    
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        if($order->gambar_desain) {
            Storage::disk('public')->delete($order->gambar_desain);
        }
        
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dihapus!');
    }

    public function processShipping(Request $request, $id_order)
    {
        $request->validate([
            'kurir' => 'required',
            'nomor_resi' => 'required',
            'biaya_ongkir' => 'nullable|numeric'
        ]);

        DB::beginTransaction();
        try {
            $order = Order::findOrFail($id_order);

            // Update atau buat data pengiriman
            \App\Models\Shipping::updateOrCreate(
                ['id_order' => $id_order],
                [
                    'kurir' => $request->kurir,
                    'nomor_resi' => $request->nomor_resi,
                    'biaya_ongkir' => $request->biaya_ongkir ?? 0,
                    'tanggal_pickup' => now(),
                    'status_pengiriman' => 'Dalam Perjalanan'
                ]
            );

            // Update status di tabel orders
            $order->update(['status_order' => 'dikirim']);

            DB::commit();
            return back()->with('success', 'Resi berhasil diinput, pesanan sedang dikirim!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pengiriman: ' . $e->getMessage());
        }
    }

    public function markAsDelivered($id_order)
    {
        DB::beginTransaction();
        try {
            $order = Order::with('shipping')->findOrFail($id_order);

            if ($order->shipping) {
                $order->shipping->update([
                    'tanggal_delivery' => now(),
                    'status_pengiriman' => 'Diterima'
                ]);
            }

            // Final status untuk pesanan
            $order->update(['status_order' => 'selesai', 
            'tanggal_selesai' => now()
            ]);

            DB::commit();
            return back()->with('success', 'Pesanan telah diterima. Transaksi Selesai!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan pesanan.');
        }
    }

    public function shippingReports()
    {
        $reports = Order::where('status_order', 'dikirim')
                    ->with('shipping')
                    ->latest()
                    ->paginate(15);

        return view('orders.shipping_reports', compact('reports'));
    }

    public function showCalculation($id_order)
    {
        // 1. Ambil data Order
        $order = Order::with('product.boms')->findOrFail($id_order);

        // 2. Ambil atau Buat data Produksi
        $production = Production::firstOrCreate(
            ['id_order' => $id_order],
            ['tahap_produksi' => 'potong']
        );

        // 3. Jika produksi baru dibuat & belum punya bahan, salin dari Master BOM
        if ($production->materials()->count() == 0) {
            $masterBoms = $order->product->boms;
            
            foreach ($masterBoms as $bom) {
                $kebutuhanPerPcs = $bom->jumlah_kebutuhan + ($bom->jumlah_kebutuhan * ($bom->persentase_waste / 100));
                $totalKebutuhan = $kebutuhanPerPcs * $order->jumlah_pesanan;

                $production->materials()->create([
                    'id_bahanbaku' => $bom->id_bahanbaku,
                    'jumlah_estimasi' => $totalKebutuhan,
                    'jumlah_realisasi' => 0,
                ]);
            }
            
            // Refresh data agar materials yang baru dibuat terbaca
            $production->load('materials.rawMaterial');
        }

        $isFinished = in_array($order->status_order, ['perlu dikirim', 'dikirim', 'selesai']);

        // 4. Olah data untuk tabel (sama seperti kodingan Anda sebelumnya)
        $results = [];
        foreach ($production->materials as $pm) {
            $material = $pm->rawMaterial;
            $butuh = $pm->jumlah_estimasi;
            
            $ketersediaan = $isFinished ? ($pm->jumlah_realisasi ?? 0) : ($material->stok ?? 0);
            
            $results[] = [
                'nama_bahanbaku' => $material->nama_bahanbaku,
                'butuh'          => $butuh,
                'satuan'         => $material->satuan,
                'ketersediaan'   => $ketersediaan,
                'stok_gudang'    => $material->stok,
                'stok_terkunci'  => $material->stok_terkunci,
                'realisasi'      => $pm->jumlah_realisasi,
                'kekurangan'     => ($butuh > $ketersediaan) ? ($butuh - $ketersediaan) : 0,
                'status'         => ($ketersediaan >= $butuh) ? 'CUKUP' : 'KURANG',
            ];
        }

        return view('orders.check', compact('production', 'order', 'isFinished', 'results'));
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Production;
use App\Models\ProductionMaterial;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $userRole = strtolower(auth()->user()->role ?? '');
        if (!in_array($userRole, ['admin', 'manajerial'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka laporan ini.');
        }

        $start_date = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $selected_statuses = $request->get('statuses', []);
        $query = DB::table('orders')
            ->join('products', 'orders.id_product', '=', 'products.id_product')
            ->select(
                'orders.created_at',
                'orders.nama_pelanggan',
                'products.nama_produk',
                'orders.jumlah_pesanan',
                'orders.total_harga', 
                'orders.status_order'
            )
            ->whereBetween('orders.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

        if (!empty($selected_statuses)) {
            $query->whereIn('orders.status_order', $selected_statuses);
        }

        $orders = $query->orderBy('orders.created_at', 'desc')->get();

        $summary = [
            'total_omzet' => $orders->sum('total_harga'),
            'total_pesanan' => $orders->count(),
            'total_qty' => $orders->sum('jumlah_pesanan'),
        ];

        return view('reports.sales', [
            'orders' => $orders,
            'summary' => $summary,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'selected_statuses' => $selected_statuses
        ]);
    }
    public function production(Request $request)
    {
        $start_date = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Filter Tahap Produksi (Array)
        $selected_stages = $request->get('stages', []);

        $query = DB::table('productions')
            ->join('orders', 'productions.id_order', '=', 'orders.id_order')
            ->join('products', 'orders.id_product', '=', 'products.id_product')
            ->select(
                'productions.created_at',
                'productions.id_order',
                'orders.nama_pelanggan',
                'orders.tahap_produksi', 
                'orders.status_order',
                'products.nama_produk',
                'orders.jumlah_pesanan'
            )
            ->whereBetween('productions.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('orders.status_order', '!=', 'menunggu bahan');

        // Terapkan Filter Tahap jika dipilih
        if (!empty($selected_stages)) {
            $query->whereIn('orders.tahap_produksi', $selected_stages);
        }

        $productions = $query->orderBy('productions.created_at', 'desc')->get();

        // Summary Box
        $summary = [
            'total_produksi' => $productions->count(),
            'siap_proses'    => $productions->where('status_order', 'siap produksi')->count(),
            'sedang_jalan'   => $productions->where('status_order', 'produksi')->count(),
            'tahap_akhir'    => $productions->whereIn('status_order', ['perlu dikirim','dikirim','selesai'])->count(),
        ];

        return view('reports.production', compact('productions', 'summary', 'start_date', 'end_date', 'selected_stages'));
    }

    public function material()
    {
        $materials = DB::table('raw_materials')
            ->select('*')
            ->orderByRaw('(stok <= stok_minimum) DESC')
            ->get();

        $summary = [
            'total_jenis' => $materials->count(),
            
            'stok_kritis' => $materials->filter(function($item) {
                return $item->stok <= $item->stok_minimum;
            })->count(),

            'total_aset'   => $materials->sum(function($item) {
                return $item->stok * ($item->harga ?? 0);
            }),
        ];

        return view('reports.material', compact('materials', 'summary'));
    }

    public function mutation(Request $request)
    {
        $start_date = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $material_id = $request->get('material_id');
        $transaction_type = $request->get('transaction_type');

        $query = DB::table('stock_movements')
            ->join('raw_materials', 'stock_movements.id_bahanbaku', '=', 'raw_materials.id_bahanbaku')
            ->select('stock_movements.*', 'raw_materials.nama_bahanbaku', 'raw_materials.satuan')
            ->whereBetween('stock_movements.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

        if ($material_id) {
            $query->where('stock_movements.id_bahanbaku', $material_id);
        }

        if ($transaction_type) {
            $query->where('stock_movements.tipe_transaksi', $transaction_type);
        }

        $mutations = $query->orderBy('stock_movements.created_at', 'desc')->get();
        $materials = DB::table('raw_materials')->select('id_bahanbaku', 'nama_bahanbaku')->get();

        return view('reports.mutation', compact('mutations', 'materials', 'start_date', 'end_date', 'material_id', 'transaction_type'));
    }

    public function salesExcel(Request $request)
    {
        // 1. Tangkap filter
        $start_date = $request->query('start_date');
        $end_date   = $request->query('end_date');
        $statuses   = $request->query('statuses');

        // 2. Build Query
        $query = DB::table('orders')
            ->join('products', 'orders.id_product', '=', 'products.id_product')
            ->select('orders.*', 'products.nama_produk');

        if ($start_date && $end_date) {
            $query->whereBetween('orders.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);
        }

        if ($statuses && is_array($statuses)) {
            $upperStatuses = array_map('strtoupper', $statuses);
            $query->whereIn('orders.status_order', $upperStatuses);
        }

        $data = $query->orderBy('orders.created_at', 'desc')->get();

        // 3. NAMA FILE DINAMIS
        // Jika tanggal kosong (user tidak filter), kita kasih teks 'Semua'
        $tgl_awal = $start_date ?: 'Awal';
        $tgl_akhir = $end_date ?: 'Sekarang';
        
        $fileName = "Laporan_Penjualan_{$tgl_awal}_{$tgl_akhir}.csv";

        // 4. Setting Header Download
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header Tabel
            fputcsv($file, ['TANGGAL', 'ORDER ID', 'PELANGGAN', 'PRODUK', 'QTY', 'TOTAL HARGA', 'STATUS']);

            foreach ($data as $row) {
                fputcsv($file, [
                    date('d/m/Y', strtotime($row->created_at)),
                    '#ORD-' . $row->id_order,
                    strtoupper($row->nama_pelanggan),
                    $row->nama_produk,
                    $row->jumlah_pesanan,
                    $row->total_harga,
                    strtoupper($row->status_order),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function productionExcel(Request $request)
    {
        // 1. Ambil filter parameter
        $start_date      = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date        = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $selected_stages = $request->input('stages', []);

        // 2. Build Query 
        $query = DB::table('productions')
            ->join('orders', 'productions.id_order', '=', 'orders.id_order')
            ->join('products', 'orders.id_product', '=', 'products.id_product')
            ->select(
                'productions.created_at',
                'productions.id_order',
                'orders.nama_pelanggan',
                'orders.tahap_produksi',
                'orders.status_order',
                'products.nama_produk',
                'orders.jumlah_pesanan'
            )
            ->whereBetween('productions.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('orders.status_order', '!=', 'menunggu bahan');

        // Terapkan Filter Multi-Select
        if (!empty($selected_stages)) {
            $query->whereIn('orders.tahap_produksi', $selected_stages);
        }

        $data = $query->orderBy('productions.created_at', 'desc')->get();

        // 3. Logika Nama File Dinamis Sesuai Aturan Mutasi
        $tgl_awal  = date('dmY', strtotime($start_date));
        $tgl_akhir = date('dmY', strtotime($end_date));

        if ($start_date === $end_date) {
            $fileName = "Laporan Produksi_{$tgl_awal}.csv";
        } else {
            $fileName = "Laporan Produksi_{$tgl_awal}-{$tgl_akhir}.csv";
        }

        // 4. Proses Stream CSV dengan Pengaman UTF-8 & Formulasi Excel
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Sisipkan UTF-8 BOM agar Excel membaca simbol/huruf lokal dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header Tabel
            fputcsv($file, ['TANGGAL MULAI', 'ORDER ID', 'PELANGGAN', 'PRODUK', 'QTY', 'TAHAP PRODUKSI', 'STATUS ORDER']);

            foreach ($data as $row) {
                $order_id_clean = " #ORD-" . $row->id_order;
                
                $status_db = strtolower($row->status_order);
                $tahap_db  = strtolower($row->tahap_produksi);
                
                if ($status_db == 'siap produksi') {
                    $label_tahap = 'MENUNGGU PRODUKSI';
                } else {
                    $label_tahap = strtoupper($tahap_db);
                }

                fputcsv($file, [
                    date('d/m/Y', strtotime($row->created_at)),
                    $order_id_clean,
                    strtoupper($row->nama_pelanggan),
                    strtoupper($row->nama_produk),
                    $row->jumlah_pesanan . ' Pcs',
                    $label_tahap,
                    strtoupper($row->status_order)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function materialExcel()
    {
        $materials = DB::table('raw_materials')
            ->select('*')
            ->orderByRaw('(stok <= stok_minimum) DESC')
            ->get();

        $fileName = "Laporan Stok Bahan Baku - " . date('dmY') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($materials) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID BAHAN', 'NAMA BAHAN BAKU', 'STOK SAAT INI', 'SATUAN', 'STATUS STOK', 'NILAI STOK (Rp)']);

            foreach ($materials as $m) {
                $isLow = $m->stok <= $m->stok_minimum;
                $status_stok = $isLow ? 'PERLU TAMBAHAN STOK' : 'STOCK AMAN';
                $nilai_stok = $m->stok * ($m->harga ?? 0);

                fputcsv($file, [
                    '#MAT-' . $m->id_bahanbaku,
                    strtoupper($m->nama_bahanbaku),
                    number_format($m->stok, 2, '.', ''),
                    strtoupper($m->satuan),
                    $status_stok,
                    $nilai_stok
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function mutationExcel(Request $request)
    {
        // 1. Ambil filter parameter
        $start_date  = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date    = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $material_id = $request->input('material_id');
        $transaction_type = $request->input('transaction_type');

        // 2. Query Data dari database
        $query = DB::table('stock_movements')
            ->join('raw_materials', 'stock_movements.id_bahanbaku', '=', 'raw_materials.id_bahanbaku')
            ->select('stock_movements.*', 'raw_materials.nama_bahanbaku', 'raw_materials.satuan')
            ->whereBetween('stock_movements.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

        if ($material_id) {
            $query->where('stock_movements.id_bahanbaku', $material_id);
        }

        if ($transaction_type) {
            $query->where('stock_movements.tipe_transaksi', $transaction_type);
        }

        $data = $query->orderBy('stock_movements.created_at', 'desc')->get();

        // 3. Logika Nama File Dinamis
        $tgl_awal  = date('dmY', strtotime($start_date));
        $tgl_akhir = date('dmY', strtotime($end_date));

        if ($start_date === $end_date) {
            $fileName = "Laporan Mutasi Stok_{$tgl_awal}.csv";
        } else {
            $fileName = "Laporan Mutasi Stok_{$tgl_awal}-{$tgl_akhir}.csv";
        }

        // 4. Proses Stream CSV
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // FIX 1: Tambahkan UTF-8 BOM di awal file agar Excel membaca simbol ± dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header Tabel CSV
            fputcsv($file, ['WAKTU TRANSAKSI', 'NOMOR REFERENSI', 'NAMA BAHAN BAKU', 'TIPE TRANSAKSI', 'JUMLAH MUTASI', 'SATUAN', 'KETERANGAN']);

            foreach ($data as $row) {
                $tipe = strtolower($row->tipe_transaksi);
                
                $symbol = match($tipe) {
                    'masuk'       => '+',
                    'penyesuaian' => '±',
                    default       => '-'
                };

                $jumlah_clean = " " . $symbol . " " . number_format($row->jumlah, 2, '.', '');

                fputcsv($file, [
                    date('d/m/Y H:i', strtotime($row->created_at)),
                    '#REF-' . $row->id_movement,
                    strtoupper($row->nama_bahanbaku),
                    strtoupper($row->tipe_transaksi),
                    $jumlah_clean,
                    strtoupper($row->satuan),
                    $row->keterangan ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function materialPdf()
    {
        $materials = DB::table('raw_materials')
            ->select('*')
            ->orderByRaw('(stok <= stok_minimum) DESC')
            ->get();

        $summary = [
            'total_jenis' => $materials->count(),
            'stok_kritis' => $materials->filter(function($item) {
                return $item->stok <= $item->stok_minimum;
            })->count(),
            'total_aset'   => $materials->sum(function($item) {
                return $item->stok * ($item->harga ?? 0);
            }),
        ];

        $fileName = "Laporan Stok Bahan Baku - " . date('dmY') . ".pdf";

        $pdf = Pdf::loadView('reports.material_pdf', compact('materials', 'summary'));
        
        return $pdf->setPaper('a4', 'portrait')->download($fileName);
    }

    public function salesPdf(Request $request)
    {
        $start_date = $request->input('start_date', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date   = $request->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));
        $selected_statuses = $request->input('statuses', []);

        $query = DB::table('orders')
            ->join('products', 'orders.id_product', '=', 'products.id_product')
            ->select(
                'orders.created_at',
                'orders.nama_pelanggan',
                'products.nama_produk',
                'orders.jumlah_pesanan',
                'orders.total_harga', 
                'orders.status_order'
            )
            ->whereBetween('orders.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

        if (!empty($selected_statuses)) {
            $query->whereIn('orders.status_order', $selected_statuses);
        }

        $orders = $query->orderBy('orders.created_at', 'desc')->get();

        $summary = [
            'total_omzet'   => $orders->sum('total_harga'),
            'total_pesanan' => $orders->count(),
            'total_qty'     => $orders->sum('jumlah_pesanan'),
        ];

        $tgl_awal  = date('dmY', strtotime($start_date));
        $tgl_akhir = date('dmY', strtotime($end_date));

        if ($start_date === $end_date) {
            $fileName = "Laporan Penjualan_{$tgl_awal}.pdf";
        } else {
            $fileName = "Laporan Penjualan_{$tgl_awal}-{$tgl_akhir}.pdf";
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sales_pdf', compact('orders', 'summary', 'start_date', 'end_date'));
        
        return $pdf->setPaper('a4', 'portrait')->download($fileName);
    }

    public function productionPdf(Request $request)
    {
        $start_date      = $request->input('start_date', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date        = $request->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));
        $selected_stages = $request->input('stages', []);

        $query = DB::table('productions')
            ->join('orders', 'productions.id_order', '=', 'orders.id_order')
            ->join('products', 'orders.id_product', '=', 'products.id_product')
            ->select(
                'productions.created_at',
                'productions.id_order',
                'orders.nama_pelanggan',
                'orders.tahap_produksi', 
                'orders.status_order',
                'products.nama_produk',
                'orders.jumlah_pesanan'
            )
            ->whereBetween('productions.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
            ->where('orders.status_order', '!=', 'menunggu bahan');

        if (!empty($selected_stages)) {
            $query->whereIn('orders.tahap_produksi', $selected_stages);
        }

        $productions = $query->orderBy('productions.created_at', 'desc')->get();

        $summary = [
            'total_produksi' => $productions->count(),
            'siap_proses'    => $productions->where('status_order', 'siap produksi')->count(),
            'sedang_jalan'   => $productions->where('status_order', 'produksi')->count(),
            'tahap_akhir'    => $productions->whereIn('status_order', ['perlu dikirim','dikirim','selesai'])->count(),
        ];

        $tgl_awal  = date('dmY', strtotime($start_date));
        $tgl_akhir = date('dmY', strtotime($end_date));

        if ($start_date === $end_date) {
            $fileName = "Laporan Produksi_{$tgl_awal}.pdf";
        } else {
            $fileName = "Laporan Produksi_{$tgl_awal}-{$tgl_akhir}.pdf";
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.production_pdf', compact('productions', 'summary', 'start_date', 'end_date'));
        
        return $pdf->setPaper('a4', 'portrait')->download($fileName);
    }

    public function mutationPdf(Request $request)
    {
        $start_date  = $request->input('start_date', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'));
        $end_date    = $request->input('end_date', \Carbon\Carbon::now()->format('Y-m-d'));
        $material_id = $request->input('material_id');
        $transaction_type = $request->input('transaction_type');

        $query = DB::table('stock_movements')
            ->join('raw_materials', 'stock_movements.id_bahanbaku', '=', 'raw_materials.id_bahanbaku')
            ->select('stock_movements.*', 'raw_materials.nama_bahanbaku', 'raw_materials.satuan')
            ->whereBetween('stock_movements.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59']);

        if ($material_id) {
            $query->where('stock_movements.id_bahanbaku', $material_id);
        }

        if ($transaction_type) {
            $query->where('stock_movements.tipe_transaksi', $transaction_type);
        }

        $mutations = $query->orderBy('stock_movements.created_at', 'desc')->get();

        $tgl_awal  = date('dmY', strtotime($start_date));
        $tgl_akhir = date('dmY', strtotime($end_date));

        if ($start_date === $end_date) {
            $fileName = "Laporan Mutasi Stok_{$tgl_awal}.pdf";
        } else {
            $fileName = "Laporan Mutasi Stok_{$tgl_awal}-{$tgl_akhir}.pdf";
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.mutation_pdf', compact('mutations', 'start_date', 'end_date'));
        
        return $pdf->setPaper('a4', 'portrait')->download($fileName);
    }

}
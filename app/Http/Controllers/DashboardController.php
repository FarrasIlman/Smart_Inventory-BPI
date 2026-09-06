<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\ProductionMaterial;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. FILTER TANGGAL GLOBAL (Berlaku untuk semua jenis dashboard)
        $start_date = $request->input('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end_date   = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $time_start = $start_date . ' 00:00:00';
        $time_end   = $end_date . ' 23:59:59';

        // 2. CEK ROLE USER YANG SEDANG LOGIN
        $role = trim(strtolower(auth()->user()->role ?? ''));

        switch ($role) {
            
            // =========================================================================
            // DASHBOARD 1: ADMIN & MANAJERIAL (FULL DATA + KEUANGAN)
            // =========================================================================
            case 'admin':
            case 'manajerial':
                $omzetJual = Order::where('status_order', 'selesai')->whereBetween('created_at', [$time_start, $time_end])->sum('total_harga');
                $modalBahan = ProductionMaterial::whereHas('production.order', function($orq) use ($time_start, $time_end) {
                    $orq->whereBetween('created_at', [$time_start, $time_end])->where('status_order', 'selesai');
                })->sum('subtotal');
                $labaBersih = $omzetJual - $modalBahan;
                $totalBelanja = Purchase::where('status_pembelian', 'diterima')->whereBetween('created_at', [$time_start, $time_end])->sum('total');

                $pipeline = $this->getPipelineData($time_start, $time_end);
                $criticalStock = RawMaterial::whereColumn('stok', '<=', 'stok_minimum')->count();
                $safeStock = RawMaterial::whereColumn('stok', '>', 'stok_minimum')->count();
                $monthlyData = ['labels' => ['OKT', 'NOV', 'DES', 'JAN', 'FEB', 'MAR'], 'revenue' => [80, 110, 125, 140, 150, 160], 'expense' => [30, 45, 55, 60, 65, 70]];
                
                $activeProduction = Order::where('status_order', 'produksi')->whereBetween('created_at', [$time_start, $time_end])->latest()->take(3)->get();
                $activeShipping = Order::whereIn('status_order', ['perlu dikirim', 'dikirim'])->whereBetween('created_at', [$time_start, $time_end])->with('shipping')->latest()->take(3)->get();

                return view('dashboard.admin', compact('omzetJual', 'labaBersih', 'totalBelanja', 'pipeline', 'criticalStock', 'safeStock', 'monthlyData', 'activeProduction', 'activeShipping', 'start_date', 'end_date'));

            
            case 'marketing': 
                return redirect()->route('cms.log');

            // =========================================================================
            // DASHBOARD 2: CUSTOMER HANDLE (PESANAN, PRODUK, PRODUKSI, PENGIRIMAN - TANPA KEUANGAN)
            // =========================================================================
            case 'customer handle':
                $pipeline = $this->getPipelineData($time_start, $time_end);
                $activeProduction = Order::where('status_order', 'produksi')->whereBetween('created_at', [$time_start, $time_end])->latest()->take(5)->get();
                $activeShipping = Order::whereIn('status_order', ['perlu dikirim', 'dikirim'])->whereBetween('created_at', [$time_start, $time_end])->with('shipping')->latest()->take(5)->get();

                return view('dashboard.customer_handle', compact('pipeline', 'activeProduction', 'activeShipping', 'start_date', 'end_date'));


            // =========================================================================
            // DASHBOARD 3: PRODUKSI (FOKUS MONITORING ALUR PRODUKSI SAJA)
            // =========================================================================
            case 'produksi':
                $pipeline = [
                    'ready_prod'    => Order::where('status_order', 'siap produksi')->whereBetween('created_at', [$time_start, $time_end])->count(),
                    'in_production' => Order::where('status_order', 'produksi')->whereBetween('created_at', [$time_start, $time_end])->count(),
                    'done'          => Order::where('status_order', 'selesai')->whereBetween('created_at', [$time_start, $time_end])->count(),
                ];
                // Mengambil list antrean produksi yang sedang aktif berjalan
                $productionList = Order::whereIn('status_order', ['siap produksi', 'produksi'])->whereBetween('created_at', [$time_start, $time_end])->orderBy('status_order', 'desc')->get();

                return view('dashboard.production', compact('pipeline', 'productionList', 'start_date', 'end_date'));


            // =========================================================================
            // DASHBOARD 4: GUDANG (STOK BAHAN, MUTASI, & SUPPLIER)
            // =========================================================================
            case 'gudang':
                $criticalStock = RawMaterial::whereColumn('stok', '<=', 'stok_minimum')->count();
                $safeStock = RawMaterial::whereColumn('stok', '>', 'stok_minimum')->count();
                
                // Ambil daftar bahan baku yang kritis untuk ditampilkan di tabel gudang
                $lowStockMaterials = RawMaterial::whereColumn('stok', '<=', 'stok_minimum')->orderBy('stok', 'asc')->take(5)->get();
                // Hitung total supplier terdaftar
                $totalSupplier = DB::table('suppliers')->count(); 

                return view('dashboard.gudang', compact('criticalStock', 'safeStock', 'lowStockMaterials', 'totalSupplier', 'start_date', 'end_date'));


            // Default jika role tidak dikenali sistem
            default:
                abort(403, 'Role akun Anda tidak memiliki otoritas masuk ke dashboard.');
        }
    }

    // Helper function untuk mempermudah perhitungan alur pesanan (pipeline)
    private function getPipelineData($time_start, $time_end)
    {
        return [
            'total'          => Order::whereBetween('created_at', [$time_start, $time_end])->count(),
            'waiting_mats'   => Order::where('status_order', 'menunggu bahan')->whereBetween('created_at', [$time_start, $time_end])->count(),
            'ready_prod'     => Order::where('status_order', 'siap produksi')->whereBetween('created_at', [$time_start, $time_end])->count(),
            'in_production'  => Order::where('status_order', 'produksi')->whereBetween('created_at', [$time_start, $time_end])->count(),
            'ready_ship'     => Order::where('status_order', 'perlu dikirim')->whereBetween('created_at', [$time_start, $time_end])->count(),
            'shipping'       => Order::where('status_order', 'dikirim')->whereBetween('created_at', [$time_start, $time_end])->count(),
            'done'           => Order::where('status_order', 'selesai')->whereBetween('created_at', [$time_start, $time_end])->count(),
        ];
    }
}
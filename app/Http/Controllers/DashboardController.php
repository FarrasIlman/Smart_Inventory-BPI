<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\ProductionMaterial;
use App\Models\RawMaterial;
use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    // --- 1. FINANCIAL SUMMARY ---
    $omzetJual = Order::where('status_order', 'selesai')->sum('total_harga');
    
    // Modal Bahan Baku
    $modalBahan = ProductionMaterial::whereHas('production.order', function($q) {
        $q->where('status_order', 'selesai');
    })->sum('subtotal');

    $labaBersih = $omzetJual - $modalBahan;

    // Total Belanja Kebutuhan
    $totalBelanja = Purchase::where('status_pembelian', 'diterima')->sum('total');

    // --- 2. OPERATIONAL STATUS (Alur Pesanan) ---
    $pipeline = [
        'total'          => Order::count(),
        'waiting_mats'   => Order::where('status_order', 'menunggu bahan')->count(),
        'ready_prod'     => Order::where('status_order', 'siap produksi')->count(),
        'in_production'  => Order::where('status_order', 'produksi')->count(),
        'finished'       => Order::where('status_order', 'perlu dikirim')->count(),
        'shipping'       => Order::where('status_order', 'dikirim')->count(),
        'done'           => Order::where('status_order', 'selesai')->count(),
    ];

    // --- 3. STOCK MONITORING ---
    $criticalStock = RawMaterial::whereColumn('stok', '<=', 'stok_minimum')->count();
    $safeStock = RawMaterial::whereColumn('stok', '>', 'stok_minimum')->count();

    // --- 4. DATA UNTUK CHART CASHFLOW BULANAN ---
    $monthlyData = [
        'labels' => ['OKT', 'NOV', 'DES', 'JAN', 'FEB', 'MAR'], // Contoh label bulan
        'revenue' => [80, 110, 125, 140, 150, 160], // Contoh data omzet (dalam jutaan)
        'expense' => [30, 45, 55, 60, 65, 70], // Contoh data belanja (dalam jutaan)
    ];

    // --- 5. LIVE MONITORING CARDS ---
    $activeProduction = Order::where('status_order', 'produksi')->latest()->take(3)->get();
    $activeShipping = Order::where('status_order', 'dikirim')->with('shipping')->latest()->take(3)->get();

    return view('dashboard', compact(
        'omzetJual', 'labaBersih', 'totalBelanja', 'pipeline', 
        'criticalStock', 'safeStock', 'monthlyData', 'activeProduction', 'activeShipping'
    ));
}
}
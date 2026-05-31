@extends('layouts.main')
@section('page_title', 'Halaman Utama')
@section('content')
<div class="p-6 md:p-10 bg-[#F8FAFC] min-h-screen">
    
    {{-- Top Header: Greeting --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard</h1>
            <p class="text-slate-500 font-medium">Selamat datang kembali, <span class="text-blue-600">{{ Auth::user()->name }}</span>. Berikut ringkasan bisnis Anda hari ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white p-2 px-4 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-2">
                <i class="fa-regular fa-calendar text-blue-500"></i>
                <span class="text-sm font-bold text-slate-700">{{ date('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Section 1: Financial Bento Grid (Score Cards) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Card Penjualan --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-50 relative overflow-hidden">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-blue-50 rounded-2xl text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 10c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z"/></svg>
                </div>
                <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full uppercase tracking-widest">Growth +12%</span>
            </div>
            <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Total Penjualan</p>
            <h2 class="text-3xl font-black text-slate-900 mt-1">Rp {{ number_format($omzetJual, 0, ',', '.') }}</h2>
            <div class="mt-4 flex items-center gap-2 text-[11px] text-slate-400">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>Data dari pesanan selesai</span>
            </div>
        </div>

        {{-- Card Laba --}}
        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <i class="fa-solid fa-chart-line text-8xl text-white"></i>
            </div>
            <div class="p-3 bg-white/10 rounded-2xl text-blue-400 w-fit mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Estimasi Laba Bersih</p>
            <h2 class="text-3xl font-black text-white mt-1">Rp {{ number_format($labaBersih, 0, ',', '.') }}</h2>
            <p class="mt-4 text-[11px] text-slate-500 italic font-medium">Keuntungan bersih dari penjualan anda</p>
        </div>

        {{-- Card Pengeluaran --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-50">
            <div class="p-3 bg-rose-50 rounded-2xl text-rose-500 w-fit mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Total Pengeluaran</p>
            <h2 class="text-3xl font-black text-slate-900 mt-1">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</h2>
            <div class="mt-4 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                <div class="bg-rose-500 h-full" style="width: 45%"></div>
            </div>
        </div>
    </div>

    {{-- Section 2: Pipeline & Stock --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        
        {{-- Pipeline Flow (Large Card) --}}
        <div class="lg:col-span-2 bg-white p-10 rounded-[3rem] shadow-sm border border-slate-50">
            <div class="flex justify-between items-center mb-10">
                <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest italic">Pesanan</h3>
                <div class="flex gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pantau pesanan</span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                @php
                    $pipelineSteps = [
                        ['label' => 'Menunggu Bahan', 'count' => $pipeline['waiting_mats'], 'icon' => 'fa-box', 'color' => 'slate'],
                        ['label' => 'Siap Produksi', 'count' => $pipeline['ready_prod'], 'icon' => 'fa-clipboard-check', 'color' => 'indigo'],
                        ['label' => 'Sedang Produksi', 'count' => $pipeline['in_production'], 'icon' => 'fa-gears', 'color' => 'blue'],
                        ['label' => 'Kirim', 'count' => $pipeline['shipping'], 'icon' => 'fa-truck-fast', 'color' => 'amber'],
                        ['label' => 'Selesai', 'count' => $pipeline['done'], 'icon' => 'fa-circle-check', 'color' => 'emerald'],
                    ];
                @endphp
                @foreach($pipelineSteps as $step)
                <div class="group cursor-default">
                    <div class="mb-3 text-center">
                        <span class="text-3xl font-black text-{{ $step['color'] }}-600 group-hover:scale-110 transition-transform block">{{ $step['count'] }}</span>
                    </div>
                    <div class="bg-{{ $step['color'] }}-50/50 p-4 rounded-2xl border border-{{ $step['color'] }}-100 text-center transition-all group-hover:bg-{{ $step['color'] }}-50">
                        <i class="fa-solid {{ $step['icon'] }} text-{{ $step['color'] }}-400 mb-2"></i>
                        <p class="text-[9px] font-black text-{{ $step['color'] }}-500 uppercase tracking-widest">{{ $step['label'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stock Alerts (Smaller Side Card) --}}
        <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-50 flex flex-col justify-center gap-8">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Stok Bahan Baku</p>
                <div class="flex items-center justify-between p-5 bg-emerald-50 rounded-3xl border border-emerald-100">
                    <div>
                        <p class="text-xs font-bold text-emerald-600 uppercase">Stok Aman</p>
                        <h4 class="text-2xl font-black text-emerald-700">{{ $safeStock }} <span class="text-xs font-medium">Item</span></h4>
                    </div>
                    <i class="fa-solid fa-shield-check text-3xl text-emerald-200"></i>
                </div>
            </div>
            <div class="flex items-center justify-between p-5 bg-rose-50 rounded-3xl border border-rose-100 animate-pulse">
                <div>
                    <p class="text-xs font-bold text-rose-600 uppercase">Stok Kurang</p>
                    <h4 class="text-2xl font-black text-rose-700">{{ $criticalStock }} <span class="text-xs font-medium">Restock</span></h4>
                </div>
                <i class="fa-solid fa-triangle-exclamation text-3xl text-rose-200"></i>
            </div>
        </div>
    </div>

    {{-- Section 3: Live Monitoring --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Monitoring Produksi --}}
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-50">
            <h3 class="text-sm font-black text-slate-800 uppercase italic mb-6">Monitoring Produksi Berjalan</h3>
            <div class="space-y-4">
                @foreach($activeProduction as $prod)
                <div class="flex items-center gap-4 p-4 hover:bg-slate-50 rounded-3xl transition-colors group">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 font-black">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-slate-700 uppercase tracking-tight">{{ $prod->nama_pelanggan }}</p>
                        <p class="text-[11px] text-slate-400 font-medium italic">{{ $prod->product->nama_produk ?? 'Kategori Produk' }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-tighter">{{ $prod->tahap_produksi }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Monitoring Pengiriman --}}
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-50">
            <h3 class="text-sm font-black text-slate-800 uppercase italic mb-6">Paket Sedang Dikirim</h3>
            <div class="space-y-4">
                @foreach($activeShipping as $ship)
                <div class="flex items-center gap-4 p-4 hover:bg-slate-50 rounded-3xl transition-colors group border border-dashed border-transparent hover:border-slate-200">
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500">
                        <i class="fa-solid fa-truck-fast text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-slate-700 uppercase tracking-tight">{{ $ship->nama_pelanggan }}</p>
                        <p class="text-[11px] text-slate-400 tracking-tighter">{{ $ship->shipping->kurir ?? 'Ekspedisi' }} • {{ $ship->shipping->nomor_resi ?? '-' }}</p>
                    </div>
                    <a href="{{ route('orders.show', $ship->id_order) }}" class="text-slate-300 hover:text-blue-500 transition-colors">
                        <i class="fa-solid fa-arrow-right-long text-lg"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
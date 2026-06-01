@extends('layouts.main')
@section('page_title', 'Dashboard Customer Handle')
@section('content')
<div class="p-6 md:p-10 bg-[#F8FAFC] min-h-screen">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">CRM Dashboard</h1>
            <p class="text-slate-500 font-medium">Halo, <span class="text-blue-600">{{ Auth::user()->nama_user }}</span>. Berikut pantauan pesanan pelanggan hari ini.</p>
        </div>
    </div>

    {{-- Form Filter Rentang Waktu --}}
    <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm mb-8">
        <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[180px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Mulai Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm outline-none">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm outline-none">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-blue-700 transition-all">Filter</button>
        </form>
    </div>

    {{-- Pipeline Alur Pesanan --}}
    <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-50 mb-10">
        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest italic mb-6">Alur Order Pelanggan</h3>
        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
            @php
                $steps = [
                    ['label' => 'Menunggu Bahan', 'count' => $pipeline['waiting_mats'], 'icon' => 'fa-box', 'color' => 'slate'],
                    ['label' => 'Siap Produksi', 'count' => $pipeline['ready_prod'], 'icon' => 'fa-clipboard-check', 'color' => 'indigo'],
                    ['label' => 'Sedang Produksi', 'count' => $pipeline['in_production'], 'icon' => 'fa-gears', 'color' => 'blue'],
                    ['label' => 'Perlu Dikirim', 'count' => $pipeline['ready_ship'], 'icon' => 'fa-boxes-packing', 'color' => 'rose'],
                    ['label' => 'Sedang Dikirim', 'count' => $pipeline['shipping'], 'icon' => 'fa-truck-fast', 'color' => 'amber'],
                    ['label' => 'Selesai', 'count' => $pipeline['done'], 'icon' => 'fa-circle-check', 'color' => 'emerald'],
                ];
            @endphp
            @foreach($steps as $s)
            <div class="text-center">
                <span class="text-2xl font-black text-{{ $s['color'] }}-600 block mb-2">{{ $s['count'] }}</span>
                <div class="bg-{{ $s['color'] }}-50/50 p-4 rounded-2xl border border-{{ $s['color'] }}-100">
                    <i class="fa-solid {{ $s['icon'] }} text-{{ $s['color'] }}-400 mb-1"></i>
                    <p class="text-[9px] font-black text-{{ $s['color'] }}-500 uppercase tracking-wide leading-tight">{{ $s['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Live Monitoring Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Produksi --}}
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-50">
            <h3 class="text-sm font-black text-slate-800 uppercase italic mb-6">Proses Produksi Aktif ({{ $pipeline['in_production'] }})</h3>
            <div class="space-y-4">
                @foreach($activeProduction as $prod)
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                    <div>
                        <p class="text-sm font-black text-slate-700 uppercase">{{ $prod->nama_pelanggan }}</p>
                        <p class="text-xs text-slate-400 font-medium italic">{{ $prod->product->nama_produk ?? 'Kategori Produk' }}</p>
                    </div>
                    <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-3 py-1 rounded-full uppercase">{{ $prod->tahap_produksi }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pengiriman --}}
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-50">
            <h3 class="text-sm font-black text-slate-800 uppercase italic mb-6">Status Logistik Paket</h3>
            <div class="space-y-4">
                @foreach($activeShipping as $ship)
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                    <div>
                        <p class="text-sm font-black text-slate-700 uppercase">{{ $ship->nama_pelanggan }}</p>
                        <p class="text-xs text-slate-400 font-mono">{{ $ship->shipping->kurir ?? 'Ekspedisi' }} • {{ $ship->shipping->nomor_resi ?? 'Belum Rilis' }}</p>
                    </div>
                    <span class="text-[9px] font-black uppercase px-2.5 py-1 rounded-lg {{ $ship->status_order == 'perlu dikirim' ? 'bg-rose-50 text-rose-600' : 'bg-amber-50 text-amber-600' }}">{{ $ship->status_order }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
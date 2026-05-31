@extends('layouts.main')

@section('page_title', 'Laporan')

@section('content')
<div class="p-6 md:p-10">
    @php
        $userRole = strtolower(auth()->user()->role ?? '');
        
        $canSales      = in_array($userRole, ['admin', 'manajerial']);
        $canProduction = in_array($userRole, ['admin', 'manajerial', 'produksi']);
        $canMaterial   = in_array($userRole, ['admin', 'manajerial', 'gudang']);
        $canMutation   = in_array($userRole, ['admin', 'manajerial', 'gudang']);
    @endphp

    {{-- Header --}}
    <div class="mb-10">
        <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Halaman Laporan</h1>
        <p class="text-slate-400 text-sm mt-1">Pilih kategori data yang ingin Anda analisis atau cetak.</p>
    </div>

    {{-- Grid Menu Laporan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- 1. Laporan Penjualan --}}
        <a href="{{ $canSales ? route('reports.sales') : 'javascript:void(0)' }}" 
           class="{{ $canSales ? 'group hover:shadow-2xl hover:shadow-blue-100' : 'cursor-not-allowed opacity-50 select-none' }} relative bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full {{ $canSales ? 'group-hover:scale-110 transition-transform duration-500' : '' }}"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Penjualan</h3>
                <p class="text-slate-400 text-xs font-medium leading-relaxed">Analisis omzet, profit per pesanan, dan tren pelanggan.</p>
            </div>
            
            @if($canSales)
                <div class="mt-8 flex items-center text-blue-600 font-bold text-xs uppercase tracking-widest group-hover:gap-2 transition-all">
                    Buka Laporan <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            @else
                <div class="mt-8 flex items-center text-slate-400 font-bold text-xs uppercase tracking-widest gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Akses Terkunci
                </div>
            @endif
        </a>

        {{-- 2. Laporan Produksi --}}
        <a href="{{ $canProduction ? route('reports.production') : 'javascript:void(0)' }}" 
           class="{{ $canProduction ? 'group hover:shadow-2xl hover:shadow-amber-100' : 'cursor-not-allowed opacity-50 select-none' }} relative bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full {{ $canProduction ? 'group-hover:scale-110 transition-transform duration-500' : '' }}"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-amber-500 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-amber-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Produksi</h3>
                <p class="text-slate-400 text-xs font-medium leading-relaxed">Pantau efisiensi factory, status BOM, dan durasi jahit.</p>
            </div>
            
            @if($canProduction)
                <div class="mt-8 flex items-center text-amber-600 font-bold text-xs uppercase tracking-widest group-hover:gap-2 transition-all">
                    Buka Laporan <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            @else
                <div class="mt-8 flex items-center text-slate-400 font-bold text-xs uppercase tracking-widest gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Akses Terkunci
                </div>
            @endif
        </a>

        {{-- 3. Laporan Bahan Baku --}}
        <a href="{{ $canMaterial ? route('reports.material') : 'javascript:void(0)' }}" 
           class="{{ $canMaterial ? 'group hover:shadow-2xl hover:shadow-emerald-100' : 'cursor-not-allowed opacity-50 select-none' }} relative bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full {{ $canMaterial ? 'group-hover:scale-110 transition-transform duration-500' : '' }}"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-emerald-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-emerald-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Stok Bahan</h3>
                <p class="text-slate-400 text-xs font-medium leading-relaxed">Cek nilai aset gudang, stok kritis, dan kebutuhan restock.</p>
            </div>
            
            @if($canMaterial)
                <div class="mt-8 flex items-center text-emerald-600 font-bold text-xs uppercase tracking-widest group-hover:gap-2 transition-all">
                    Buka Laporan <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            @else
                <div class="mt-8 flex items-center text-slate-400 font-bold text-xs uppercase tracking-widest gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Akses Terkunci
                </div>
            @endif
        </a>

        {{-- 4. Laporan Mutasi --}}
        <a href="{{ $canMutation ? route('reports.mutation') : 'javascript:void(0)' }}" 
           class="{{ $canMutation ? 'group hover:shadow-2xl hover:shadow-indigo-100' : 'cursor-not-allowed opacity-50 select-none' }} relative bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full {{ $canMutation ? 'group-hover:scale-110 transition-transform duration-500' : '' }}"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-indigo-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-indigo-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2 uppercase tracking-tight">Mutasi Stok</h3>
                <p class="text-slate-400 text-xs font-medium leading-relaxed">Log lengkap barang masuk dan keluar secara real-time.</p>
            </div>
            
            @if($canMutation)
                <div class="mt-8 flex items-center text-indigo-600 font-bold text-xs uppercase tracking-widest group-hover:gap-2 transition-all">
                    Buka Laporan <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            @else
                <div class="mt-8 flex items-center text-slate-400 font-bold text-xs uppercase tracking-widest gap-2">
                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Akses Terkunci
                </div>
            @endif
        </a>

    </div>

    {{-- Info Note --}}
    <div class="mt-12 bg-slate-50 border border-dashed border-slate-200 p-6 rounded-3xl">
        <p class="text-slate-500 text-xs font-medium flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path></svg>
            Tips: Hak akses menu di atas otomatis disesuaikan dengan wewenang jabatan akun Anda yang terdaftar di database.
        </p>
    </div>
</div>
@endsection
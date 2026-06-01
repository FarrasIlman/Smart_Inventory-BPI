@extends('layouts.main')
@section('page_title', 'Dashboard Gudang')
@section('content')
<div class="p-6 md:p-10 bg-[#F8FAFC] min-h-screen">
    
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Logistics & Supply Dashboard</h1>
        <p class="text-slate-500 font-medium">Akses Logistik Gudang Bahan Baku.</p>
    </div>

    {{-- Kategori Ringkasan Stok Gudang --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-3xl border border-emerald-100 flex justify-between items-center shadow-sm">
            <div>
                <p class="text-xs font-bold text-emerald-600 uppercase">Material Aman</p>
                <h4 class="text-2xl font-black text-emerald-700">{{ $safeStock }} <span class="text-xs font-medium">SKU</span></h4>
            </div>
            <i class="fa-solid fa-shield-check text-3xl text-emerald-200"></i>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-rose-100 flex justify-between items-center shadow-sm">
            <div>
                <p class="text-xs font-bold text-rose-600 uppercase">Perlu Restock</p>
                <h4 class="text-2xl font-black text-rose-700">{{ $criticalStock }} <span class="text-xs font-medium">SKU</span></h4>
            </div>
            <i class="fa-solid fa-triangle-exclamation text-3xl text-rose-200"></i>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 flex justify-between items-center shadow-sm">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">Mitra Supplier</p>
                <h4 class="text-2xl font-black text-slate-700">{{ $totalSupplier }} <span class="text-xs font-medium">Vendor</span></h4>
            </div>
            <i class="fa-solid fa-truck-field text-3xl text-slate-300"></i>
        </div>
    </div>

    {{-- Alert Tabel SKU Kritis --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <h3 class="text-sm font-black text-rose-600 uppercase italic mb-6">⚠️ Peringatan Bahan Baku Hampir Habis</h3>
        <table class="w-full text-left text-xs font-bold">
            <thead>
                <tr class="bg-slate-50 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <th class="p-4 px-6">ID SKU</th>
                    <th class="p-4">Nama Bahan Baku</th>
                    <th class="p-4 text-center">Sisa Stok</th>
                    <th class="p-4 text-center">Batas Minimum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($lowStockMaterials as $m)
                <tr class="text-slate-700">
                    <td class="p-4 px-6 font-mono text-slate-400">#MAT-{{ $m->id_bahanbaku }}</td>
                    <td class="p-4 uppercase">{{ $m->nama_bahanbaku }}</td>
                    <td class="p-4 text-center text-rose-600 font-black">{{ number_format($m->stok, 2) }} {{ $m->satuan }}</td>
                    <td class="p-4 text-center text-slate-400">{{ number_format($m->stok_minimum, 2) }} {{ $m->satuan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
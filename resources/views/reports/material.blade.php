@extends('layouts.main')

@section('page_title', 'Laporan Inventaris Bahan')

@section('content')
<div class="p-6 md:p-10">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <nav class="flex text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">
                <a href="{{ route('reports.index') }}" class="hover:text-indigo-500 transition-colors">Laporan</a>
                <span class="mx-2">/</span>
                <span class="text-slate-800">Bahan Baku</span>
            </nav>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">
                Stok <span class="text-indigo-500">Bahan Baku</span>
            </h1>
        </div>

        {{-- Visual Buttons Only --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.material.pdf') }}" class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-sm flex items-center group">
                <svg class="w-4 h-4 mr-2 text-rose-500 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                PDF Report
            </a>
            <a href="{{ route('reports.material.excel') }}" class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-sm flex items-center group">
                <svg class="w-4 h-4 mr-2 text-emerald-500 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Excel
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="w-16 h-16 text-indigo-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Jenis Bahan</p>
            <h3 class="text-3xl font-black text-slate-800">{{ $summary['total_jenis'] }} <span class="text-xs text-slate-300 italic">SKU</span></h3>
        </div>

        <div class="bg-rose-500 p-8 rounded-[2.5rem] text-white shadow-xl shadow-rose-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-20">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest mb-2 opacity-80">Stok Dibawah Minimum</p>
            <h3 class="text-3xl font-black">{{ $summary['stok_kritis'] }} <span class="text-xs opacity-60 italic">Bahan</span></h3>
        </div>

        <div class="bg-slate-900 p-8 rounded-[2.5rem] text-white shadow-xl shadow-slate-200 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-20 text-indigo-400">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.63 1m-5.26 0C9.92 9.407 10.89 9 12 9m0 8c-1.11 0-2.08-.407-2.63-1m5.26 0c.08-.593-.89-1-2-1M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H10a1 1 0 01-1-1v-4z"/></svg>
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest mb-2 opacity-60">Estimasi Nilai Aset</p>
            <h3 class="text-3xl font-black text-indigo-400">Rp {{ number_format($summary['total_aset'], 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest">List Inventaris</h4>
            <span class="text-[10px] font-bold text-slate-400 italic">*Diurutkan berdasarkan stok terendah</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-8 py-6">Nama Bahan Baku</th>
                        <th class="px-8 py-6 text-center">Stok Saat Ini</th>
                        <th class="px-8 py-6 text-center">Satuan</th>
                        <th class="px-8 py-6">Status Stok</th>
                        <th class="px-8 py-6 text-right">Nilai Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                @forelse($materials as $m)
                @php
                    $isLow = $m->stok <= $m->stok_minimum;
                @endphp
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="px-8 py-5">
                        <span class="block font-black text-slate-700 uppercase text-sm">{{ $m->nama_bahanbaku }}</span>
                        <span class="text-[9px] text-slate-400 font-bold uppercase">ID: #MAT-{{ $m->id_bahanbaku }}</span>
                    </td>
                    
                    {{-- Stok Saat Ini (Fisik) --}}
                    <td class="px-8 py-5 text-center">
                        <span class="text-lg font-black {{ $isLow ? 'text-rose-500' : 'text-slate-700' }}">
                            {{ number_format($m->stok, 2) }}
                        </span>
                    </td>

                    <td class="px-8 py-5 text-center">
                        <span class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-500 uppercase">{{ $m->satuan }}</span>
                    </td>

                    {{-- Status Keamanan --}}
                    <td class="px-8 py-5">
                        @if($isLow)
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-rose-50 text-rose-600 rounded-full border border-white shadow-sm">
                                <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span>
                                <span class="text-[9px] font-black uppercase tracking-wider">Perlu Tambahan Stok</span>
                            </div>
                        @else
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-full border border-white shadow-sm">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                <span class="text-[9px] font-black uppercase tracking-wider text-nowrap">Stock Aman</span>
                            </div>
                        @endif
                    </td>

                    <td class="px-8 py-5 text-right font-black text-slate-500 group-hover:text-indigo-600 transition-colors">
                        Rp {{ number_format($m->stok * ($m->harga ?? 0), 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center text-slate-300 italic">Gudang kosong.</td>
                </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
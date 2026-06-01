@extends('layouts.main')
@section('page_title', 'Dashboard Produksi')
@section('content')
<div class="p-6 md:p-10 bg-[#F8FAFC] min-h-screen">
    
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Factory Dashboard</h1>
        <p class="text-slate-500 font-medium">Akses Divisi Produksi Konveksi Bumiputera Persada Industri.</p>
    </div>

    {{-- Score Cards Produksi (Ubah dari grid-cols-2 menjadi md:grid-cols-3) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Antrean Siap Produksi --}}
        <div class="bg-white p-6 rounded-3xl border border-indigo-100 flex justify-between items-center shadow-sm">
            <div>
                <p class="text-xs font-bold text-indigo-500 uppercase tracking-widest">Antrean Siap Produksi</p>
                <h3 class="text-3xl font-black text-indigo-700 mt-1">{{ $pipeline['ready_prod'] }} <span class="text-xs font-normal">Order</span></h3>
            </div>
            <i class="fa-solid fa-clipboard-check text-4xl text-indigo-100"></i>
        </div>

        {{-- Sedang Berjalan --}}
        <div class="bg-white p-6 rounded-3xl border border-blue-100 flex justify-between items-center shadow-sm">
            <div>
                <p class="text-xs font-bold text-blue-500 uppercase tracking-widest">Sedang Berjalan Di Workshop</p>
                <h3 class="text-3xl font-black text-blue-700 mt-1">{{ $pipeline['in_production'] }} <span class="text-xs font-normal">Proses</span></h3>
            </div>
            <i class="fa-solid fa-gears text-4xl text-blue-100 animate-spin" style="animation-duration: 10s;"></i>
        </div>

        {{-- BARU: Produksi Selesai (Emerald Theme) --}}
        <div class="bg-white p-6 rounded-3xl border border-emerald-100 flex justify-between items-center shadow-sm">
            <div>
                <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest">Produksi Telah Selesai</p>
                <h3 class="text-3xl font-black text-emerald-700 mt-1">{{ $pipeline['done'] ?? 0 }} <span class="text-xs font-normal">Selesai</span></h3>
            </div>
            <i class="fa-solid fa-circle-check text-4xl text-emerald-100"></i>
        </div>
    </div>

    {{-- Tabel Monitoring Antrean Kerja --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <h3 class="text-sm font-black text-slate-800 uppercase italic mb-6">Daftar Antrean & Manufaktur Berjalan</h3>
        <table class="w-full text-left text-xs font-bold">
            <thead>
                <tr class="bg-slate-50 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <th class="p-4 px-6">Pelanggan</th>
                    <th class="p-4 text-center">Qty Pesanan</th>
                    <th class="p-4 text-center">Tahapan Kerja</th>
                    <th class="p-4">Status Internal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($productionList as $p)
                <tr>
                    <td class="p-4 px-6 text-slate-700 uppercase">{{ $p->nama_pelanggan }}</td>
                    <td class="p-4 text-center text-slate-500">{{ $p->jumlah_pesanan }} Pcs</td>
                    <td class="p-4 text-center">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] uppercase">{{ $p->tahap_produksi ?? 'Menunggu' }}</span>
                    </td>
                    <td class="p-4">
                        <span class="text-[10px] uppercase {{ $p->status_order == 'produksi' ? 'text-amber-500' : 'text-indigo-500' }}">{{ $p->status_order }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-8 text-center text-slate-400 italic">Tidak ada antrean manufaktur periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
@extends('layouts.main')

@section('page_title', 'Manajemen Pembelian')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Pembelian</h1>
            <p class="text-slate-400 text-xs mt-1">Manajemen pembelian bahan baku.</p>
        </div>
        
        {{-- PROTEKSI: Tombol Tambah Pembelian hanya muncul dan bisa diakses oleh Admin --}}
        @if(strtolower(auth()->user()->role ?? '') == 'admin')
        <a href="{{ route('purchases.create') }}" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all">
            + Tambah Pembelian
        </a>
        @endif
    </div>

    <div class="flex flex-col md:flex-row gap-3 justify-between items-start md:items-center">
        <form method="GET" action="{{ route('purchases.index') }}" class="flex gap-3 w-full md:w-auto">

            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari ID Pembelian..."
                class="bg-white border border-slate-200 px-4 py-2.5 rounded-xl text-sm w-full md:w-64">

            <select name="status" onchange="this.form.submit()"
                class="bg-white border border-slate-200 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600">
                
                <option value="">Semua Status</option>
                <option value="dipesan" {{ request('status') == 'dipesan' ? 'selected' : '' }}>
                    Dipesan
                </option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>
                    Diterima
                </option>
                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>
                    Dikembalikan
                </option>

            </select>

            <button class="bg-slate-800 hover:bg-blue-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition-all">
                Cari
            </button>

        </form>
    </div>

    <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
            <h3 class="text-slate-800 font-bold text-lg">Daftar Pembelian</h3>
            <p class="text-slate-400 text-xs">Riwayat pembelian bahan baku.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/50 text-slate-400 uppercase text-[10px] font-bold tracking-widest">
                    <tr>
                        <th class="px-6 py-5 text-center">ID</th>
                        <th class="px-6 py-5 text-center">Supplier</th>
                        <th class="px-6 py-5 text-center">Tanggal</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 text-center">Total Item</th>
                        <th class="px-6 py-5 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse($purchases as $p)
                    <tr class="hover:bg-slate-50/50 transition-all">

                        <td class="px-6 py-6 text-center">
                            <p class="font-black text-slate-700">
                                #{{ $p->id_purchase }}
                            </p>
                        </td>

                        <td class="px-6 py-6 text-center">
                            <p class="font-bold text-slate-800 text-sm">
                                {{ $p->supplier->nama_supplier ?? '-' }}
                            </p>
                        </td>

                        <td class="px-6 py-6 text-center">
                            <p class="text-sm text-slate-600">
                                {{ \Carbon\Carbon::parse($p->tanggal_pembelian)->format('d/m/Y') }}
                            </p>
                        </td>

                        <td class="px-6 py-6 text-center">
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest
                                {{ $p->status_pembelian == 'dipesan' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                {{ $p->status_pembelian == 'diterima' ? 'bg-green-50 text-green-600 border border-green-100' : '' }}
                                {{ $p->status_pembelian == 'dikembalikan' ? 'bg-red-50 text-red-600 border border-red-100' : '' }}">
                                {{ $p->status_pembelian }}
                            </span>
                        </td>

                        <td class="px-6 py-6 text-center">
                            <p class="font-bold text-slate-800">
                                {{ $p->details->count() }} Item
                            </p>
                        </td>

                        <td class="px-6 py-6 text-center">
                            <a href="{{ route('purchases.show', $p->id_purchase) }}"
                                class="bg-white border border-slate-200 hover:text-blue-600 px-4 py-2 rounded-xl text-xs font-bold shadow-sm">
                                Lihat Detail
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-slate-400">
                            Tidak ada data pembelian...
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
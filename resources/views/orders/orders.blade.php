@extends('layouts.main')

@section('page_title', 'Manajemen Pesanan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Pesanan</h1>
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-600 px-6 py-4 rounded-2xl mb-6 font-bold text-sm">
                    {{ session('success') }}
                </div>
            @endif
            <p class="text-slate-400 text-xs mt-1">Halaman utama pengelolaan order dan monitoring produksi.</p>
        </div>
        
        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <div class="relative flex-1 sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pelanggan..." 
                    class="w-full bg-white border border-slate-200 pl-10 pr-4 py-2.5 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/>
                </svg>
            </div>
            
            <select name="status" onchange="this.form.submit()" class="bg-white border border-slate-200 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 outline-none cursor-pointer">
                <option value="">Semua Status</option>
                <option value="menunggu bahan" {{ request('status') == 'menunggu bahan' ? 'selected' : '' }}>Menunggu Bahan</option>
                <option value="siap produksi" {{ request('status') == 'siap produksi' ? 'selected' : '' }}>Siap Produksi</option>
                <option value="produksi" {{ request('status') == 'produksi' ? 'selected' : '' }}>Dalam Produksi</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Pesanan Selesai</option>
            </select>

            <a href="{{ route('orders.create') }}" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 flex items-center justify-center shrink-0 transition-all">
                + Tambah Pesanan
            </a>
        </form>
    </div>

    <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/30 text-center sm:text-left">
            <h3 class="text-slate-800 font-bold text-lg">Daftar Pesanan Masuk</h3>
            <p class="text-slate-400 text-xs">Kelola order pelanggan dan cek kesediaan bahan baku produksi.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50/50 text-slate-400 uppercase text-[10px] font-bold tracking-widest border-b border-slate-50">
                    <tr>
                        <th class="px-6 py-5 text-center">Pelanggan & Produk</th>
                        <th class="px-6 py-5 text-center">Rincian Size</th>
                        <th class="px-6 py-5 text-center">Total Qty</th>
                        <th class="px-6 py-5 text-center">Timeline</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        
                        <!-- Pelanggan -->
                        <td class="px-6 py-6 align-middle">
                            <div class="flex items-center justify-start gap-3 min-h-[56px] pl-4">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold border border-blue-100 uppercase text-xs shrink-0">
                                    {{ substr($order->nama_pelanggan ?? '-', 0, 1) }}
                                </div>
                                <div class="text-left min-w-[120px]">
                                    <p class="font-bold text-slate-800 text-sm leading-tight mb-0.5">
                                        {{ $order->nama_pelanggan }}
                                    </p>
                                    <p class="text-[10px] text-blue-500 font-bold uppercase tracking-wider">
                                        {{ $order->product->nama_produk ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Size -->
                        <td class="px-6 py-6 align-middle">
                            <div class="flex flex-wrap gap-1.5 justify-center max-w-[160px] mx-auto min-h-[56px] items-center">
                                @forelse($order->details as $detail)
                                    <div class="flex flex-col items-center justify-center min-w-[32px] h-10 bg-white border border-slate-200 rounded-lg shadow-sm">
                                        <span class="text-[8px] font-black text-slate-400 uppercase leading-none mb-1">
                                            {{ $detail->size }}
                                        </span>
                                        <span class="text-[11px] font-bold text-slate-700 leading-none">
                                            {{ $detail->quantity }}
                                        </span>
                                    </div>
                                @empty
                                    <span class="text-[10px] text-slate-400 italic">
                                        -
                                    </span>
                                @endforelse
                            </div>
                        </td>

                        <!-- Qty -->
                        <td class="px-6 py-6 text-center align-middle">
                            <div class="flex flex-col justify-center items-center min-h-[56px]">
                                <p class="text-lg font-black text-slate-800 leading-none">
                                    {{ $order->jumlah_pesanan }}
                                </p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                                    Pieces
                                </p>
                            </div>
                        </td>

                        <!-- Timeline -->
                        <td class="px-6 py-6 text-center align-middle">
                            <div class="flex flex-col items-center justify-center space-y-1 min-h-[56px]">
                                <span class="text-[9px] font-bold text-slate-400 uppercase">
                                    Mulai: {{ \Carbon\Carbon::parse($order->tanggal_pesan)->format('d/m/y') }}
                                </span>
                                <div class="w-8 h-px bg-slate-100"></div>
                                <span class="text-[11px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-md border border-red-100">
                                    DL: {{ \Carbon\Carbon::parse($order->deadline)->format('d/m/y') }}
                                </span>
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-6 text-center align-middle">
                            <div class="flex items-center justify-center min-h-[56px]">
                                <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest inline-block 
                                    {{ $order->status_order == 'menunggu bahan' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                                    {{ $order->status_order == 'siap produksi' ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' }}
                                    {{ $order->status_order == 'produksi' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : '' }}
                                    {{ $order->status_order == 'selesai' ? 'bg-green-50 text-green-600 border border-green-100' : '' }}">
                                    {{ $order->status_order }}
                                </span>
                            </div>
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-6 text-center align-middle">
                            <div class="flex flex-col gap-2 max-w-[140px] mx-auto min-h-[72px] justify-center">
                                <a href="{{ route('orders.show', $order->id_order) }}" 
                                    class="flex items-center justify-center w-full bg-white border border-slate-200 hover:border-blue-400 hover:text-blue-600 text-slate-600 px-3 py-2 rounded-xl text-[10px] font-bold transition-all shadow-sm">
                                    Lihat Detail
                                </a>
                                
                                <a href="{{ route('orders.check', $order->id_order) }}" 
                                    class="flex items-center justify-center w-full bg-slate-800 hover:bg-blue-600 text-white px-3 py-2 rounded-xl text-[10px] font-bold transition-all shadow-md">
                                    Hitung Keperluan
                                </a>
                            </div>
                        </td>

                        @include('orders.modal-detail', ['order' => $order])

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <p class="text-slate-400 font-medium italic">Tidak ada data pesanan...</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@extends('layouts.main')

@section('content')
@php
    $cukup = collect($results)->where('status', 'CUKUP')->count();
    $total = count($results);
    $persen = ($total > 0) ? ($cukup / $total) * 100 : 0;
    
    if($isFinished) { $persen = 100; }
@endphp

<div x-data="{ 
    showConfirm: false, 
    showShortage: false, 
    isComplete: {{ $persen == 100 ? 'true' : 'false' }} 
}">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}" 
               class="group flex items-center justify-center p-3 bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 rounded-2xl transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                    {{ $isFinished ? 'Rekapitulasi Bahan Baku' : 'Kalkulasi Bahan Baku' }}
                </h1>
                <p class="text-slate-400 text-xs italic">
                    {{ $isFinished ? 'Laporan pemakaian untuk:' : 'Analisis untuk pesanan:' }} 
                    <span class="text-blue-600 font-bold">{{ $order->nama_pelanggan }}</span> 
                    ({{ $order->jumlah_pesanan }} Pcs)
                </p>
            </div>
        </div>
    </div>

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-600 px-6 py-4 rounded-2xl mb-6 text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        {{-- Tabel Kebutuhan --}}
        <div class="lg:col-span-2 bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
                <h3 class="text-slate-800 font-bold text-sm uppercase tracking-widest">
                    {{ $isFinished ? 'Rekapitulasi Pemakaian Bahan' : 'Rincian Kebutuhan Komponen' }}
                </h3>
                @if($isFinished)
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                    Selesai Produksi
                </span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Bahan Baku</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                {{ $isFinished ? 'Estimasi' : 'Butuh' }}
                            </th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                {{ $isFinished ? 'Realisasi' : 'Ketersediaan Stok' }}
                            </th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($results as $res)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-8 py-6">
                                <p class="font-black text-slate-700 text-sm mb-1 uppercase tracking-tight">{{ $res['nama_bahanbaku'] }}</p>
                                {{-- Alert Kurang hanya muncul jika BELUM selesai --}}
                                @if(!$isFinished && $res['kekurangan'] > 0)
                                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-red-50 rounded-lg border border-red-100">
                                        <span class="text-[9px] text-red-600 font-black uppercase tracking-tighter italic">
                                            ⚠️ Butuh Tambahan: +{{ number_format($res['kekurangan'], 2) }} {{ $res['satuan'] }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-sm font-black text-blue-600">{{ number_format($res['butuh'], 2) }}</span> 
                                <span class="text-[9px] text-slate-400 font-bold uppercase ml-0.5">{{ $res['satuan'] }}</span>
                            </td>
                            
                            <td class="px-6 py-6 text-center">
                                {{-- Jika SELESAI, tampilkan Realisasi. Jika BELUM, tampilkan Ketersediaan --}}
                                @if($isFinished)
                                    <p class="text-sm font-black text-slate-700">
                                        {{ number_format($res['realisasi'] ?? 0, 2) }} 
                                        <span class="text-[9px] text-slate-400 font-bold uppercase ml-0.5">{{ $res['satuan'] }}</span>
                                    </p>
                                    <span class="text-[9px] text-emerald-500 font-bold uppercase tracking-tighter">Sudah Terpakai</span>
                                @else
                                    <p class="text-sm font-black {{ $res['ketersediaan'] < $res['butuh'] ? 'text-red-500' : 'text-slate-700' }}">
                                        {{ number_format($res['ketersediaan'], 2) }} 
                                        <span class="text-[9px] text-slate-400 font-bold uppercase ml-0.5">{{ $res['satuan'] }}</span>
                                    </p>
                                    <div class="flex items-center justify-center gap-2 mt-1">
                                        <span class="text-[9px] text-slate-400 font-medium uppercase tracking-tighter">Gudang: {{ number_format($res['stok_gudang'], 2) }}</span>
                                        @if(($res['stok_terkunci'] ?? 0) > 0)
                                            <span class="text-[9px] text-blue-500 font-bold uppercase tracking-tighter bg-blue-50 px-1 rounded" title="Total stok yang sedang dikunci">
                                                🔒 {{ number_format($res['stok_terkunci'], 2) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-6 text-center">
                                @if($isFinished)
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        TERPENUHI
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $res['status'] == 'CUKUP' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                        {{ $res['status'] }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-10 text-center text-slate-400 font-medium italic">Data BOM belum diatur.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Summary --}}
        <div class="space-y-6">
            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8 sticky top-6">
                <h3 class="font-black text-slate-800 text-lg mb-6 tracking-tight italic">Ringkasan Produksi</h3>
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between items-start text-sm border-b border-slate-50 pb-3">
                        <span class="text-slate-400 font-medium italic">Produk:</span>
                        <span class="font-black text-slate-800 text-right w-1/2">{{ $order->product->nama_produk }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-b border-slate-50 pb-3">
                        <span class="text-slate-400 font-medium italic">Kesiapan Bahan:</span>
                        <span class="font-black {{ $persen == 100 ? 'text-green-600' : 'text-amber-500' }}">
                            {{ round($persen) }}% Terpenuhi
                        </span>
                    </div>
                </div>

                @if($isFinished)
                    {{-- Tampilan Jika Produksi Sudah Selesai --}}
                    <div class="w-full bg-emerald-50 border-2 border-dashed border-emerald-200 p-6 rounded-[2rem] text-center">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-xs font-black text-emerald-700 uppercase tracking-widest">Produksi Selesai</p>
                        <p class="text-[10px] text-emerald-500 mt-1 italic italic">Data dikunci untuk pelaporan</p>
                    </div>
                @elseif($order->status_order == 'menunggu bahan' || $order->status_order == 'siap produksi')
                    <button @click="isComplete ? showConfirm = true : showShortage = true" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2 group text-xs uppercase tracking-widest">
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Mulai Produksi Sekarang
                    </button>
                @else
                    <div class="w-full bg-slate-100 text-slate-400 py-4 rounded-2xl font-black text-center text-xs uppercase tracking-widest border border-slate-200">
                        Pesanan Sedang Diproduksi
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL 1: KONFIRMASI (Jika Bahan Cukup) --}}
    <div x-show="showConfirm" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" x-cloak x-transition>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showConfirm = false"></div>
        <div class="bg-white w-full max-w-md rounded-[40px] p-10 relative z-10 shadow-2xl text-center">
            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-6 rotate-3">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" stroke-width="2"/></svg>
            </div>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Konfirmasi Produksi</h3>
            <p class="text-slate-400 text-sm mt-2 mb-8 italic">Bagaimana Anda ingin memproses stok?</p>
            <div class="space-y-4 text-left">
                <form action="{{ route('orders.startProduction', ['id' => $order->id_order, 'deduct' => 'yes']) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-slate-900 text-white py-5 rounded-3xl font-bold text-sm hover:bg-blue-600 transition-all flex flex-col items-center">
                        <span>Kunci & Amankan Bahan</span>
                        <span class="text-[10px] opacity-40 font-normal italic">Bahan di-booking agar tidak dipakai order lain</span>
                    </button>
                </form>
                <form action="{{ route('orders.startProduction', ['id' => $order->id_order, 'deduct' => 'no']) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-white border-2 border-slate-100 text-slate-600 py-5 rounded-3xl font-bold text-sm hover:border-blue-400 transition-all flex flex-col items-center">
                        <span>Hanya Update Status</span>
                        <span class="text-[10px] text-slate-400 font-normal italic">Produksi dimulai tanpa mengunci stok</span>
                    </button>
                </form>
                <button @click="showConfirm = false" class="w-full text-slate-400 text-xs font-black uppercase tracking-widest pt-2 hover:text-red-500 transition-colors">Batal</button>
            </div>
        </div>
    </div>

    {{-- MODAL 2: PERINGATAN (Jika Bahan KURANG) --}}
    <div x-show="showShortage" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" x-cloak x-transition>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showShortage = false"></div>
        <div class="bg-white w-full max-w-md rounded-[40px] p-10 relative z-10 shadow-2xl text-center">
            <div class="w-20 h-20 bg-red-50 text-red-600 rounded-3xl flex items-center justify-center mx-auto mb-6 -rotate-3">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Produksi Ditolak!</h3>
            <p class="text-slate-400 text-sm mt-2 mb-8 italic">Ketersediaan bahan baku belum mencukupi. Pastikan stok fisik tersedia dan tidak sedang dikunci order lain.</p>
            <div class="space-y-4">
                <a href="{{ route('suppliers.index') }}" 
                class="w-full bg-slate-900 text-white py-5 rounded-3xl font-bold text-sm hover:bg-blue-600 transition-all flex items-center justify-center gap-2 shadow-xl shadow-slate-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2"/>
                    </svg>
                    Hubungi Supplier
                </a>
                <button @click="showShortage = false" class="w-full text-slate-400 text-xs font-black uppercase tracking-widest pt-2 hover:text-slate-600 transition-all">Tutup</button>
            </div>
        </div>
    </div>

</div>
@endsection
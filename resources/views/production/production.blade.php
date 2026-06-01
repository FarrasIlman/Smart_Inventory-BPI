@extends('layouts.main')

@section('page_title', 'Produksi')

@section('content')
<div class="space-y-8 pb-20">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Monitoring Produksi</h1>
            <p class="text-slate-400 text-sm italic">Pantau dan kelola setiap tahapan pengerjaan di workshop.</p>
        </div>
        
        {{-- Search & Filter Bar --}}
        <form action="{{ route('production.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pelanggan..." 
                    class="w-full bg-white border border-slate-200 rounded-2xl pl-10 pr-4 py-3 text-xs focus:ring-2 focus:ring-blue-500 outline-none w-64 transition-all shadow-sm">
                <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            </div>
            
            <select name="status" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-600 outline-none cursor-pointer shadow-sm">
                <option value="">Semua Status Aktif</option>
                <option value="siap produksi" {{ request('status') == 'siap produksi' ? 'selected' : '' }}>Siap Produksi</option>
                <option value="produksi" {{ request('status') == 'produksi' ? 'selected' : '' }}>Sedang Produksi</option>
                <option value="perlu dikirim" {{ request('status') == 'perlu dikirim' ? 'selected' : '' }}>Perlu Dikirim</option>
                <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>

            @if(request('search') || request('status'))
                <a href="{{ route('production.index') }}" class="text-xs font-black text-red-500 uppercase tracking-widest hover:underline ml-2">Reset</a>
            @endif
        </form>
    </div>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 px-6 py-4 rounded-3xl mb-6 text-sm font-bold flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Grid Monitoring --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($orders as $order)
        @php
            $stages = ['potong', 'branding', 'jahit', 'finishing', 'quality check', 'selesai'];
            $currentIdx = array_search($order->tahap_produksi, $stages);
            $progress = ($currentIdx === false) ? 0 : (($currentIdx + 1) / count($stages)) * 100;
            $isOverdue = \Carbon\Carbon::parse($order->deadline)->isPast() && $order->status_order != 'selesai';
            
            // INDIKATOR BARU: Cek wewenang pengerjaan khusus Admin & Produksi
            $canUpdateProgress = in_array(strtolower(auth()->user()->role ?? ''), ['admin', 'produksi']);
        @endphp

        <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm p-8 flex flex-col h-full transition-all hover:shadow-xl hover:shadow-slate-200/50 relative overflow-hidden group"
             x-data="{ showQuickStart: false, showFinishModal: false }">
            
            {{-- Status Badge --}}
            <div class="absolute top-0 right-0 px-6 py-2 rounded-bl-3xl text-[9px] font-black uppercase tracking-widest
            {{ $order->status_order == 'siap produksi' ? 'bg-amber-100 text-amber-600' : '' }}
            {{ $order->status_order == 'produksi' ? 'bg-blue-100 text-blue-600' : '' }}
            {{ $order->status_order == 'perlu dikirim' ? 'bg-rose-100 text-rose-600' : '' }}
            {{ $order->status_order == 'dikirim' ? 'bg-indigo-100 text-indigo-600' : '' }}
            {{ $order->status_order == 'selesai' ? 'bg-green-100 text-green-600' : '' }}">
            {{ $order->status_order }}
            </div>

            <div class="mb-6">
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">ORDER #{{ $order->id_order }}</span>
                <h3 class="text-xl font-black text-slate-800 leading-tight mt-1">{{ $order->nama_pelanggan }}</h3>
                <p class="text-sm text-slate-400 font-medium mt-1">
                    {{ $order->product->nama_produk }} <span class="mx-1 text-slate-200">•</span> {{ $order->jumlah_pesanan }} Pcs
                </p>
            </div>

            {{-- Progress Bar --}}
            <div class="mb-8 p-5 bg-slate-50 rounded-3xl border border-slate-100/50">
                <div class="flex justify-between items-end mb-3">
                    <span class="text-[10px] font-black text-slate-400 uppercase">Progress: <span class="text-slate-800">{{ $order->tahap_produksi ?? 'Menunggu' }}</span></span>
                    <span class="text-lg font-black text-blue-600">{{ round($progress) }}%</span>
                </div>
                <div class="w-full bg-white h-3 rounded-full overflow-hidden p-0.5 border border-slate-200">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $progress }}%"></div>
                </div>
            </div>

            {{-- Logic Action Sektor Proteksi --}}
            <div class="flex-1 flex flex-col justify-center">
                @if($order->status_order == 'siap produksi')
                    <div class="text-center py-6 bg-amber-50 rounded-[32px] border border-amber-100 border-dashed">
                        <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest {{ $canUpdateProgress ? 'mb-4' : '' }}">Bahan Sudah Lengkap</p>
                        
                        @if($canUpdateProgress)
                            {{-- Aktif untuk Admin & Tim Produksi --}}
                            <button @click="showQuickStart = true" 
                                class="px-8 py-3.5 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-amber-200 transition-all flex items-center gap-2 mx-auto group">
                                <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Mulai Produksi Sekarang
                            </button>
                        @else
                            {{-- Read-only badge untuk jabatan lain --}}
                            <span class="inline-block mt-1 px-4 py-2 bg-white border border-amber-200 text-amber-600 text-[9px] font-black uppercase rounded-xl tracking-widest">
                                ⏳ Menunggu Mulai Produksi
                            </span>
                        @endif
                    </div>
                @elseif($order->status_order == 'produksi')
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-4">
                        {{ $canUpdateProgress ? 'Update Tahapan Sekarang:' : 'Posisi Tahapan Pengerjaan:' }}
                    </label>
                    
                    <div class="grid grid-cols-2 gap-2 mb-6">
                        @foreach($stages as $s)
                            @if($canUpdateProgress)
                                {{-- Form klik aktif khusus Admin / Produksi --}}
                                <form action="{{ route('production.updateStage', $order->id_order) }}" method="POST">
                                    @csrf
                                    <button type="submit" name="tahap" value="{{ $s }}" 
                                        class="w-full py-3 rounded-xl text-[9px] font-black uppercase transition-all border tracking-widest
                                        {{ $order->tahap_produksi == $s 
                                            ? 'bg-blue-600 text-white border-blue-600 shadow-lg shadow-blue-200' 
                                            : 'bg-white text-slate-400 border-slate-100 hover:border-blue-300 hover:text-blue-600' }}">
                                        {{ $s }}
                                    </button>
                                </form>
                            @else
                                {{-- Garis lini masa statis mati untuk user non-produksi --}}
                                <div class="w-full py-3 rounded-xl text-[9px] font-black text-center uppercase border tracking-widest cursor-default
                                    {{ $order->tahap_produksi == $s 
                                        ? 'bg-blue-50 text-blue-600 border-blue-200 shadow-sm' 
                                        : 'bg-slate-50/50 text-slate-300 border-slate-100' }}">
                                    {{ $s }}
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- SEKTOR TOMBOL SELESAIKAN PRODUKSI --}}
                    @if($canUpdateProgress)
                        <button @click="showFinishModal = true" 
                            class="w-full py-4 bg-slate-900 hover:bg-green-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 group">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Selesaikan Produksi
                        </button>
                    @else
                        <div class="w-full py-3.5 bg-slate-100 border border-slate-200 text-slate-400 font-black text-[10px] text-center rounded-2xl uppercase tracking-widest">
                            🔒 Sedang Dikerjakan Di Workshop
                        </div>
                    @endif
                @else
                    <div class="text-center py-6 bg-green-50 rounded-[32px] border border-green-100 border-dashed">
                        <p class="text-[10px] font-black text-green-600 uppercase tracking-widest mb-1">Produksi Selesai</p>
                        <p class="text-[9px] text-green-500 font-medium italic">Sudah melewati Quality Check</p>
                    </div>
                @endif
            </div>

            {{-- Footer Info --}}
            <div class="mt-8 pt-6 border-t border-dashed border-slate-100 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Target Deadline</span>
                    <span class="text-xs font-bold {{ $isOverdue ? 'text-red-500 underline' : 'text-slate-600' }}">
                        {{ date('d M Y', strtotime($order->deadline)) }}
                    </span>
                </div>
                
                @php
                    $userRole = strtolower(auth()->user()->role ?? '');
                    $targetRoute = in_array($userRole, ['admin', 'produksi']) 
                        ? route('production.cuttingForm', $order->id_order) 
                        : route('orders.show', $order->id_order);
                @endphp

                <a href="{{ $targetRoute }}" 
                class="p-3 bg-slate-50 text-slate-400 rounded-2xl hover:bg-blue-50 hover:text-blue-600 transition-all shadow-sm"
                title="{{ in_array($userRole, ['admin', 'produksi']) ? 'Lihat Form Potong' : 'Lihat Detail Pesanan' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
                    </svg>
                </a>

            </div>

            {{-- REKAYASA POHON ELEMEN MODAL: Hanya di-render browser jika hak akses terpenuhi --}}
            @if($canUpdateProgress)
                {{-- MODAL QUICK START --}}
                <div x-show="showQuickStart" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak x-transition>
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showQuickStart = false"></div>
                    <div class="bg-white w-full max-w-sm rounded-[40px] p-10 relative z-10 shadow-2xl text-center">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-6 rotate-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight mb-2">Aktifkan Produksi?</h3>
                        <p class="text-slate-400 text-xs mb-8 italic">Status pesanan akan diperbarui menjadi <span class="text-blue-600 font-bold">Produksi</span>.</p>
                        <div class="space-y-3">
                            <form action="{{ route('orders.startProduction', ['id' => $order->id_order, 'deduct' => 'no']) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-bold text-[11px] uppercase tracking-widest hover:bg-blue-600 transition-all">Ya, Update Status</button>
                            </form>
                            <button @click="showQuickStart = false" class="w-full text-slate-400 text-[10px] font-black uppercase mt-2 hover:text-red-500 tracking-widest">Batal</button>
                        </div>
                    </div>
                </div>

                {{-- MODAL FINISH PRODUCTION --}}
                <div x-show="showFinishModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak x-transition>
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showFinishModal = false"></div>
                    <div class="bg-white w-full max-w-xl rounded-[40px] p-10 relative z-10 shadow-2xl overflow-hidden">
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight mb-2">Konfirmasi Realisasi Bahan</h3>
                        <p class="text-slate-400 text-xs mb-8 italic">Input jumlah bahan yang benar-benar digunakan untuk pesanan ini.</p>

                        @if($order->production)
                        <form action="{{ route('orders.finishProduction', $order->production->id_production) }}" method="POST">
                            @csrf
                            <div class="space-y-4 max-h-[40vh] overflow-y-auto pr-2 mb-8">
                                @foreach($order->production->materials as $pm)
                                <div class="flex items-center justify-between p-5 bg-slate-50 rounded-3xl border border-slate-100">
                                    <div class="flex-1">
                                        <p class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $pm->rawMaterial->nama_bahanbaku }}</p>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Estimasi Awal: <span class="text-blue-600">{{ $pm->jumlah_estimasi }} {{ $pm->rawMaterial->satuan }}</span></p>
                                    </div>
                                    <div class="w-32">
                                        <label class="text-[9px] font-black text-blue-600 uppercase block mb-1 tracking-widest">Realisasi ({{ $pm->rawMaterial->satuan }})</label>
                                        <input type="number" step="any" name="realization_{{ $pm->id_bahanbaku }}" value="{{ $pm->jumlah_estimasi }}" class="w-full p-3 bg-white border border-slate-200 rounded-xl text-sm font-black text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none shadow-sm transition-all" required>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="flex flex-col gap-3 mt-6">
                                <button type="submit" class="w-full bg-slate-900 text-white py-5 rounded-3xl font-black text-[11px] uppercase tracking-widest hover:bg-green-600 transition-all shadow-xl shadow-slate-100">
                                    Simpan & Selesaikan Pesanan
                                </button>
                                <button type="button" @click="showFinishModal = false" class="w-full text-slate-400 text-[10px] font-black uppercase mt-2 hover:text-red-500 tracking-widest transition-colors">
                                    Kembali
                                </button>
                            </div>
                        </form>
                        @else
                            <p class="text-center text-red-500 font-bold py-10">Data produksi tidak ditemukan.</p>
                        @endif
                    </div>
                </div>
            @endif

        </div>
        @empty
        <div class="col-span-full py-24 text-center bg-white rounded-[50px] border-2 border-dashed border-slate-100 shadow-sm">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2"/></svg>
            </div>
            <h3 class="text-slate-800 font-black text-xl mb-2">Tidak Ada Data Produksi</h3>
            <p class="text-slate-400 text-sm italic">Belum ada pesanan yang sesuai dengan filter pencarian Anda.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
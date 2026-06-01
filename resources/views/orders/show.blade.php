@extends('layouts.main')

@section('content')
<div class="space-y-6" x-data="{ openDelete:false }">

    <div class="flex items-center justify-between">
        <div>
            <nav class="flex text-slate-400 text-[10px] uppercase font-bold tracking-widest mb-2">
                <a href="{{ route('orders.index') }}" class="hover:text-blue-600 transition-colors">Pesanan</a>
                <span class="mx-2">/</span>
                <span class="text-slate-800">Detail Pesanan #{{ $order->id_order }}</span>
            </nav>
            <h1 class="text-2xl font-black text-slate-800">Detail Pesanan</h1>
        </div>

        <a href="{{ route('orders.index') }}" class="bg-white border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all shadow-sm">
            ← Kembali ke Daftar
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8">
                <h3 class="text-slate-800 font-bold text-lg mb-6 flex items-center">
                    <span class="w-2 h-6 bg-blue-600 rounded-full mr-3"></span>
                    Informasi Produksi
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Pelanggan</p>
                            <p class="text-base font-bold text-slate-800">{{ $order->nama_pelanggan }}</p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Produk yang Dipesan</p>
                            <p class="text-base font-bold text-blue-600">{{ $order->product->nama_produk }}</p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No Telepon</p>
                            <p class="text-base font-bold text-slate-800">{{ $order->no_telepon ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Alamat</p>
                            <p class="text-base font-bold text-slate-800">{{ $order->alamat ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Kuantitas</p>
                            <p class="text-base font-bold text-slate-800">{{ $order->jumlah_pesanan }} Pieces</p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Pesanan</p>
                            <span class="inline-block mt-1 px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] font-black uppercase border border-amber-100 italic">
                                {{ $order->status_order }}
                            </span>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Harga Satuan</p>
                            <p class="text-base font-bold text-green-600">
                                Rp {{ number_format($order->harga_satuan ?? 0, 0, ',', '.') }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Harga</p>
                            <p class="text-lg font-black text-green-700">
                                Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8">
                <h3 class="text-slate-800 font-bold text-lg mb-6">Distribusi Ukuran</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                    @foreach($order->details as $detail)
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1">{{ $detail->size }}</p>
                        <p class="text-xl font-black text-slate-800">{{ $detail->quantity }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="space-y-6" x-data="{ openDelete: false }">

            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8">
                <h3 class="text-slate-800 font-bold text-lg mb-6 tracking-tight">Preview Desain</h3>
                
                <div class="aspect-square bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden mb-8">
                    @if($order->gambar_desain)
                        <img src="{{ asset('storage/' . $order->gambar_desain) }}" class="w-full h-full object-contain p-4">
                    @else
                        <div class="text-slate-300 text-center">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <p class="text-[10px] font-bold uppercase tracking-widest">Tidak ada desain</p>
                        </div>
                    @endif
                </div>
                
                @if($order->status_order == 'perlu dikirim')
                    <div class="mt-6 space-y-4">
                        <div class="p-1 bg-white border border-slate-100 rounded-3xl shadow-sm">
                            <div class="p-5">
                                <div class="flex items-center space-x-4 mb-5">
                                    <div class="flex-shrink-0 w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center border border-slate-100">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-black uppercase tracking-widest text-slate-400">Step Berikutnya</h4>
                                        <p class="text-sm font-bold text-slate-700">Atur Pengiriman</p>
                                    </div>
                                </div>

                                <button onclick="document.getElementById('modalShipping').classList.remove('hidden')" 
                                        class="w-full py-4 bg-slate-900 hover:bg-blue-600 text-white rounded-2xl font-bold text-sm transition-all duration-300 shadow-xl shadow-slate-200 flex items-center justify-center space-x-2 group">
                                    <span>Input Resi & Kirim</span>
                                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if($order->status_order == 'dikirim')
                    <div class="mt-6 p-1 bg-white border border-slate-100 rounded-[2rem] shadow-sm overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center space-x-4 mb-5">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center border border-blue-100">
                                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">Status Pengiriman</h4>
                                    <p class="text-sm font-bold text-slate-700">Sedang Dalam Perjalanan</p>
                                </div>
                            </div>

                            <button onclick="document.getElementById('modalDetailShipping').classList.remove('hidden')" 
                                    class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-sm transition-all shadow-xl shadow-slate-200 flex items-center justify-center space-x-2">
                                <span>Lihat Detail Pengiriman</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if($order->status_order == 'selesai')
                    {{-- --- MODE SELESAI (TAMPILKAN REALISASI) --- --}}
                    <div class="space-y-6">
                        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-center">
                            <div class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center mx-auto mb-2 shadow-lg shadow-emerald-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <h4 class="text-emerald-800 font-black uppercase text-[10px] tracking-widest">Pesanan Selesai</h4>
                        </div>

                        <div class="space-y-2">
                            <h4 class="text-slate-400 font-bold text-[10px] uppercase tracking-widest px-1">Realisasi Bahan</h4>
                            @php $totalModal = 0; @endphp
                            @foreach($order->production->materials as $pm)
                                @php $totalModal += $pm->subtotal; @endphp
                                <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-700 uppercase leading-tight">{{ $pm->rawMaterial->nama_bahanbaku }}</p>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $pm->jumlah_realisasi }} {{ $pm->rawMaterial->satuan }}</p>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-700">Rp{{ number_format($pm->subtotal, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="pt-4 border-t border-dashed border-slate-200 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-bold uppercase text-[9px] tracking-tighter">Total Biaya Bahan</span>
                                <span class="text-slate-800 font-black text-sm">Rp{{ number_format($totalModal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="font-bold 5 px text-blue-600">Estimasi Laba</span>
                                <span class="font-black text-sm text-blue-600">Rp{{ number_format($order->total_harga - $totalModal, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <a href="{{ route('orders.index') }}" class="block w-full py-3.5 bg-slate-900 text-white rounded-xl text-center font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-all">
                            Kembali ke Pesanan
                        </a>
                    </div>

                @else
                    {{-- --- MODE BELUM SELESAI (TAMPILKAN TOMBOL AKSI) --- --}}
                    @if(in_array($order->status_order, ['menunggu bahan', 'siap produksi', 'produksi']))
                    @php
                        $role = strtolower(auth()->user()->role ?? '');
                        $isAdmin = ($role == 'admin');
                        $canEditDelete = in_array($role, ['admin', 'customer handle']);
                        $canProduction = in_array($role, ['admin', 'produksi']);
                    @endphp

                    <div class="space-y-3">
                        {{-- 1. Akses Tombol Hitung Kebutuhan Bahan: Hanya untuk Admin murni --}}
                        @if($isAdmin)
                            <a href="{{ route('orders.check', $order->id_order) }}" 
                                class="flex items-center justify-center w-full bg-slate-900 text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-slate-100 hover:bg-blue-600 transition-all">
                                Hitung Kebutuhan Bahan
                            </a>
                        @endif

                        {{-- BARU: Akses Tombol Lihat Form Potong - Terbuka untuk Admin & Produksi jika status bukan 'menunggu bahan' --}}
                        @if($canProduction && $order->status_order != 'menunggu bahan')
                            <a href="{{ route('production.cuttingForm', $order->id_order) }}" 
                                class="flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-xl shadow-blue-100 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Lihat Form Potong
                            </a>
                        @endif

                        {{-- 2. Akses Tombol Edit & Hapus: Terbuka untuk Admin & Customer Handle --}}
                        @if($canEditDelete)
                            <div class="grid grid-cols-2 gap-3">
                                <a href="{{ route('orders.edit', $order->id_order) }}"
                                    class="flex items-center justify-center bg-blue-50 text-blue-600 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest border border-blue-100 hover:bg-blue-600 hover:text-white transition-all">
                                    Edit
                                </a>

                                <button @click="openDelete = true"
                                    class="flex items-center justify-center bg-red-50 text-red-600 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest border border-red-100 hover:bg-red-500 hover:text-white transition-all">
                                    Hapus
                                </button>
                            </div>
                        @endif
                    </div>
                    @endif
                @endif
            </div>

            {{-- MODAL HAPUS PESANAN --}}
            <div x-show="openDelete" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] px-4" 
                x-cloak>
                <div class="bg-white rounded-[32px] p-8 w-full max-w-sm text-center shadow-2xl" @click.away="openDelete = false">
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight">Hapus Pesanan?</h3>
                    <p class="text-xs text-slate-400 mb-8 px-4 font-medium leading-relaxed">Data yang dihapus tidak bisa dikembalikan. Seluruh riwayat produksi akan ikut terhapus.</p>

                    <div class="grid grid-cols-2 gap-3">
                        <button @click="openDelete = false"
                            class="w-full bg-slate-50 text-slate-400 py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-slate-100 transition-all">
                            Batal
                        </button>

                        <form action="{{ route('orders.destroy', $order->id_order) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full bg-red-500 text-white py-3 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-red-100 hover:bg-red-600 transition-all">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalShipping" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity" onclick="document.getElementById('modalShipping').classList.add('hidden')"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md transform overflow-hidden rounded-[2.5rem] bg-white p-10 shadow-[0_32px_64px_-12px_rgba(0,0,0,0.14)] transition-all">
            
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-blue-50 text-blue-600 mb-6">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Kirim Pesanan</h3>
                <p class="text-slate-400 text-sm mt-2">Lengkapi detail pengiriman untuk pelanggan.</p>
            </div>

            <form action="{{ route('orders.shipping.process', $order->id_order) }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="group">
                    <label class="block text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1 mb-2 group-focus-within:text-blue-500 transition-colors">Ekspedisi</label>
                    <div class="relative">
                        <input type="text" name="kurir" placeholder="Contoh: JNE / J&T / Sicepat" 
                               class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-500/10 focus:bg-white focus:ring-4 focus:ring-blue-500/5 rounded-2xl p-4 font-bold text-slate-700 outline-none transition-all placeholder:text-slate-300 placeholder:font-medium" required>
                    </div>
                </div>

                <div class="group">
                    <label class="block text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1 mb-2 group-focus-within:text-blue-500 transition-colors">Nomor Resi</label>
                    <div class="relative">
                        <input type="text" name="nomor_resi" placeholder="Masukkan resi pengiriman" 
                               class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-500/10 focus:bg-white focus:ring-4 focus:ring-blue-500/5 rounded-2xl p-4 font-bold text-slate-700 outline-none transition-all placeholder:text-slate-300 placeholder:font-medium" required>
                    </div>
                </div>

                <div class="group">
                    <label class="block text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 ml-1 mb-2 group-focus-within:text-blue-500 transition-colors">Biaya Ongkir</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 font-black text-slate-300">Rp</span>
                        <input type="number" name="biaya_ongkir" value="0" 
                               class="w-full bg-slate-50 border-2 border-transparent focus:border-blue-500/10 focus:bg-white focus:ring-4 focus:ring-blue-500/5 rounded-2xl p-4 pl-12 font-bold text-slate-700 outline-none transition-all" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-5 bg-slate-900 hover:bg-blue-600 text-white rounded-[1.5rem] font-black text-sm shadow-xl shadow-slate-200 transition-all duration-300 transform active:scale-[0.98]">
                        KONFIRMASI & KIRIM
                    </button>
                    <button type="button" onclick="document.getElementById('modalShipping').classList.add('hidden')" 
                            class="w-full py-4 mt-2 text-slate-400 hover:text-slate-600 font-bold text-xs transition-colors">
                        Mungkin Nanti
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modalDetailShipping" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-opacity" onclick="document.getElementById('modalDetailShipping').classList.add('hidden')"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-[2.5rem] bg-white shadow-[0_32px_64px_-12px_rgba(0,0,0,0.14)] transition-all">
            
            <div class="px-10 pt-10 pb-6 border-b border-slate-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Detail Pengiriman</h3>
                        <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest font-bold">Resi: {{ $order->shipping->nomor_resi ?? '-' }}</p>
                    </div>
                    <button onclick="document.getElementById('modalDetailShipping').classList.add('hidden')" class="text-slate-300 hover:text-slate-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            <div class="p-10 space-y-8">
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Penerima & No. Telepon</label>
                        <p class="text-sm font-black text-slate-800">{{ $order->nama_pelanggan }} <span class="text-slate-400 font-medium ml-2">({{ $order->no_telepon }})</span></p>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Alamat Lengkap</label>
                        <p class="text-sm font-bold text-slate-600 leading-relaxed italic">"{{ $order->alamat }}"</p>
                    </div>
                </div>

                <hr class="border-slate-50">

                @if($order->shipping)
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Kurir / Ekspedisi</label>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase">{{ $order->shipping->kurir ?? '-' }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Tanggal Dikirim</label>
                        <p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($order->shipping->tanggal_pickup)->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Ongkos Kirim</label>
                        <p class="text-sm font-black text-emerald-600">Rp {{ number_format($order->shipping->biaya_ongkir, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endif

                <div class="pt-6">
                    <form action="{{ route('orders.complete', $order->id_order) }}" method="POST" onsubmit="return confirm('Pastikan barang benar-benar sudah sampai ke pelanggan. Lanjutkan?')">
                        @csrf
                        <button type="submit" class="w-full py-5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-[1.5rem] font-black text-sm shadow-xl shadow-emerald-100 transition-all transform active:scale-[0.98] flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>KONFIRMASI BARANG DITERIMA</span>
                        </button>
                    </form>
                    <p class="text-center text-[10px] text-slate-400 mt-4 italic font-medium">Klik konfirmasi hanya jika pesanan telah selesai sepenuhnya.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
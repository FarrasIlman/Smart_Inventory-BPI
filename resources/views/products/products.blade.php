@extends('layouts.main')
@section('page_title', 'Master Produk')
@section('content')
<div class="space-y-10" x-data="{ showAddModal: false, showEditModal: false, currentProduct: {} }">
    
    {{-- Header Section: Desain Lebih Minimalis --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-slate-100">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter">Kelola Data Produk</h1>
            <p class="text-slate-500 text-sm italic">Katalog produk & artikel konveksi Anda secara real-time.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <form action="{{ route('products.index') }}" method="GET" class="relative group">
                <input type="text" name="search" x-model="search" placeholder="Cari kode atau nama..." 
                    class="bg-white border border-slate-200 rounded-full pl-11 pr-4 py-3 text-xs focus:ring-2 focus:ring-blue-300 focus:border-blue-400 outline-none w-72 shadow-sm transition-all group-hover:border-slate-300">
                <svg class="w-5 h-5 text-slate-400 absolute left-4 top-3 transition-colors group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
            </form>
            
            <button @click="showAddModal = true" class="bg-blue-600 hover:bg-slate-950 text-white px-7 py-3.5 rounded-full text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-100 flex items-center gap-2 group">
                <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Tambah
            </button>
        </div>
    </div>

    {{-- Grid List: Compact Card (4 Kolom di Laptop) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($products as $p)
        <div class="bg-white rounded-[30px] border border-slate-100 shadow-sm overflow-hidden group hover:shadow-2xl hover:shadow-slate-200/50 hover:-translate-y-1.5 transition-all duration-300 relative flex flex-col">
            
            {{-- Category Badge: Diperkecil --}}
            <div class="absolute top-3.5 left-3.5 z-10">
                <span class="bg-white/95 backdrop-blur-sm px-3.5 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest text-blue-700 shadow-sm border border-slate-50">
                    {{ $p->kategori_produk ?? ($p->category->name ?? 'Umum') }}
                </span>
            </div>

            {{-- Image Area: Kotak 1:1, Zoom Effect --}}
            <div class="aspect-square bg-slate-50 relative overflow-hidden flex items-center justify-center border-b border-slate-100">
                @if($p->gambar_produk)
                    <img src="{{ asset('storage/' . $p->gambar_produk) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                @else
                    <div class="flex flex-col items-center gap-2 opacity-25">
                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">No Photo</span>
                    </div>
                @endif
            </div>

            {{-- Content Area: Hierarki Jelas, Padat --}}
            <div class="p-6 flex flex-col flex-grow justify-between">
                <div>
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-1">ID Artikel #{{ $p->id_product }}</p>
                    <h3 class="font-black text-slate-900 uppercase text-sm leading-tight line-clamp-2 min-h-[2.5rem]">{{ $p->nama_produk }}</h3>
                </div>
                
                {{-- Bagian Bawah: Harga Pop & Aksi Slim --}}
                <div class="mt-6 pt-5 border-t border-slate-50 flex justify-between items-end">
                    <div class="flex flex-col">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Estimasi Harga</p>
                        <div class="bg-emerald-50 text-emerald-700 px-3.5 py-1.5 rounded-xl border border-emerald-100/50 inline-block">
                            <span class="text-base font-black tracking-tighter">
                                Rp {{ number_format($p->estimasi_harga, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Actions: Ikon Bersih --}}
                    <div class="flex gap-1">
                        <button @click="currentProduct = {{ $p }}; showEditModal = true" 
                            class="p-3 bg-slate-50 text-slate-400 rounded-xl hover:bg-blue-50 hover:text-blue-600 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

                        <form action="{{ route('products.destroy', $p->id_product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-3 bg-slate-50 text-slate-400 rounded-xl hover:bg-red-50 hover:text-red-600 transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
            <div class="col-span-full py-24 text-center bg-white rounded-[40px] border-2 border-dashed border-slate-100 flex flex-col items-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="1.5"/></svg>
                </div>
                <h3 class="text-slate-800 font-black text-xl mb-1">Daftar Product Kosong</h3>
                <p class="text-sm text-slate-400 italic">Silakan klik tombol 'Tambah' untuk mengisi katalog pertama Anda.</p>
            </div>
        @endforelse
    </div>

    {{-- MODAL TAMBAH --}}
    <div x-show="showAddModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak x-transition>
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showAddModal = false"></div>
        
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl relative z-10 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100">
                <h3 class="text-xl font-bold text-slate-800">Tambah Produk Baru</h3>
                <p class="text-slate-500 text-xs mt-1">Masukkan detail informasi produk secara lengkap.</p>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-5">
                @csrf
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Nama Produk</label>
                    <input type="text" name="nama_produk" required 
                        class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="Masukkan nama produk">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Kategori</label>
                        <select name="id_categories" required 
                            class="w-full border border-slate-200 rounded-lg px-3 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id_categories }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Harga Estimasi</label>
                        <input type="number" name="estimasi_harga" 
                            class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                            placeholder="Rp 0">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-widest">Deskripsi Produk</label>
                    <textarea name="deskripsi" 
                        {{-- Gunakan x-model="currentProduct.deskripsi" khusus untuk Modal Edit --}}
                        class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all" 
                        rows="3" placeholder="Jelaskan detail produk..."></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Foto Produk</label>
                    <input type="file" name="gambar_produk" 
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-500 file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-50 mt-8">
                    <button type="button" @click="showAddModal = false" 
                        class="px-5 py-2.5 text-sm font-semibold text-slate-500 hover:text-red-600 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                        class="bg-slate-900 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-sm hover:bg-blue-600 transition-all">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- MODAL Edit --}}
    <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak x-transition>
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showEditModal = false"></div>
        
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl relative z-10 overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-100 text-center">
                <h3 class="text-xl font-bold text-slate-800">Edit Data Produk</h3>
                <p class="text-slate-500 text-xs mt-1">ID Produk: <span x-text="currentProduct.id_product" class="font-bold"></span></p>
            </div>

            <form :action="`/products/${currentProduct.id_product}`" method="POST" enctype="multipart/form-data" class="p-8 space-y-5">
                @csrf
                @method('PUT')
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-widest">Nama Produk</label>
                    <input type="text" name="nama_produk" x-model="currentProduct.nama_produk" required 
                        class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-widest">Kategori</label>
                        <select name="id_categories" x-model="currentProduct.id_categories" required 
                            class="w-full border border-slate-200 rounded-lg px-3 py-3 text-sm">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id_categories }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-widest">Estimasi Harga</label>
                        <input type="number" name="estimasi_harga" x-model="currentProduct.estimasi_harga"
                            class="w-full border border-slate-200 rounded-lg px-4 py-3 text-sm outline-none">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-widest">Ganti Foto (Opsional)</label>
                    <input type="file" name="gambar_produk" class="w-full text-xs text-slate-500">
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-50 mt-8">
                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-semibold text-slate-400 hover:text-red-600 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-sm hover:bg-slate-900 transition-all uppercase tracking-widest">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>


</div>
@endsection
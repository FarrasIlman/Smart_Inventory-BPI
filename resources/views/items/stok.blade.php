@extends('layouts.main')

@section('page_title', 'Stok Bahan Baku')

@section('content')
<div x-data="{ openModal: false, fileName: '' }">

    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h3 class="text-slate-800 font-bold text-xl tracking-tight">Daftar Inventaris Bahan</h3>
            <p class="text-slate-400 text-xs mt-1">Total ada <span class="text-blue-600 font-bold">{{ $items->count() }}</span> jenis bahan</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <form action="{{ route('items.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari bahan..." 
                        class="w-full bg-white border border-slate-200 pl-10 pr-4 py-2.5 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                
                <div class="relative w-full sm:w-44">
                    <select name="status" onchange="this.form.submit()" 
                        class="w-full bg-white border border-slate-200 pl-4 pr-10 py-2.5 rounded-xl text-sm outline-none appearance-none font-semibold text-slate-600 cursor-pointer focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">Semua Status</option>
                        <option value="aman" {{ request('status') == 'aman' ? 'selected' : '' }}>🟢 Aman</option>
                        <option value="kurang" {{ request('status') == 'kurang' ? 'selected' : '' }}>🔴 Kurang</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                </div>
            </form>

            @if(auth()->user()->role != 'manajerial')
            <button @click="openModal = true; fileName = ''" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Tambah Bahan
            </button>
            @endif
        </div>
    </div>

    {{-- Alert Section --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-4 py-3 rounded-xl mb-6 text-sm font-bold flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Table Section --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 text-center w-24">Gambar</th>
                    <th class="px-8 py-5">Nama Bahan Baku</th>
                    <th class="px-8 py-5 text-center">Stok Tersedia</th>
                    <th class="px-8 py-5">Satuan</th>
                    <th class="px-8 py-5 text-center">Status</th>
                    <th class="px-8 py-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($items as $item)
                <tr class="hover:bg-slate-50/50 transition-colors" x-data="{ editModal: false, restockModal: false, editFileName: '' }">
                    {{-- Gambar --}}
                    <td class="px-8 py-4 text-center">
                        @if($item->gambar_bahan)
                            <img src="{{ asset('storage/' . $item->gambar_bahan) }}" class="w-12 h-12 rounded-xl object-cover border border-slate-100 shadow-sm mx-auto">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center border border-dashed border-slate-200 mx-auto text-slate-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                        @endif
                    </td>

                    {{-- Nama --}}
                    <td class="px-8 py-5 font-bold text-slate-700 text-base">{{ $item->nama_bahanbaku }}</td>

                    {{-- Stok Tersedia (Modern Style) --}}
                    <td class="px-8 py-5 text-center align-middle">
                        @php
                            $available = $item->stok - $item->stok_terkunci;
                            $textColor = $available <= $item->stok_minimum ? 'text-red-600' : 'text-slate-800';
                        @endphp
                        <div class="inline-flex flex-col items-center">
                            <div class="flex items-baseline gap-1">
                                <span class="text-xl font-black tracking-tighter {{ $textColor }}">
                                    {{ number_format($available, 2) }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $item->satuan }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-2">
                                <div class="flex items-center px-2 py-1 rounded-lg bg-slate-50 border border-slate-100 shadow-sm">
                                    <span class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">Gudang: {{ number_format($item->stok, 2) }}</span>
                                </div>
                                @if($item->stok_terkunci > 0)
                                <div class="flex items-center px-2 py-1 rounded-lg bg-blue-50 border border-blue-100 shadow-sm" title="Sedang diproduksi">
                                    <span class="text-[8px] font-black text-blue-600 uppercase tracking-tighter">🔒 {{ number_format($item->stok_terkunci, 2) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Satuan --}}
                    <td class="px-8 py-5">
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded uppercase tracking-wide">{{ $item->satuan }}</span>
                    </td>

                    {{-- Status --}}
                    <td class="px-8 py-5 text-center">
                        @if($available <= $item->stok_minimum)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-[10px] font-bold uppercase tracking-widest border border-red-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-2 animate-pulse"></span> Kurang
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-green-50 text-green-600 text-[10px] font-bold uppercase tracking-widest border border-green-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span> Aman
                            </span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-8 py-5 text-right space-x-1">
                    @if(auth()->user()->role != 'manajerial')
                        {{-- Tombol Aksi --}}
                        <button @click="restockModal = true" class="text-emerald-600 font-bold py-2 px-3 rounded-lg hover:bg-emerald-50 transition-all text-xs">Restock</button>
                        
                        <button @click="editModal = true" class="text-blue-600 font-bold py-2 px-3 rounded-lg hover:bg-blue-50 transition-all text-xs">Edit</button>
                        
                        <form action="{{ route('items.destroy', $item->id_bahanbaku) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus {{ $item->nama_bahanbaku }}?')" class="text-red-400 font-bold py-2 px-3 rounded-lg hover:bg-red-50 transition-all text-xs">Hapus</button>
                        </form>

                        {{-- MODAL RESTOCK --}}
                        {{-- Tambahkan 'fixed inset-0' dan 'z-index' tinggi supaya melayang di atas tabel --}}
                        <div x-show="restockModal" 
                            class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" 
                            style="display: none;" x-cloak x-transition>
                            
                            <div class="bg-white w-full max-w-sm rounded-[32px] shadow-2xl p-8 text-left" @click.away="restockModal = false">
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black text-slate-800 tracking-tight">Restock Bahan</h3>
                                        <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">{{ $item->nama_bahanbaku }}</p>
                                    </div>
                                </div>

                                <form action="{{ route('items.restock', $item->id_bahanbaku) }}" method="POST" class="space-y-4">
                                    @csrf 
                                    @method('PUT')
                                    
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Jumlah Tambahan ({{ $item->satuan }})</label>
                                        <input type="number" step="any" name="amount" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 font-bold text-lg text-emerald-600" placeholder="0.00" required>
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Harga Beli per Unit (Rp)</label>
                                        <input type="number" name="price_per_unit" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none focus:ring-2 focus:ring-emerald-500 font-bold" placeholder="Contoh: 50000" required>
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Catatan / No. Nota</label>
                                        <input type="text" name="note" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none text-xs" placeholder="Opsional">
                                    </div>

                                    <div class="pt-4 flex flex-col gap-2">
                                        <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all">Konfirmasi Restock</button>
                                        <button type="button" @click="restockModal = false" class="w-full py-3 text-slate-400 font-bold text-[10px] uppercase tracking-widest">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- MODAL EDIT --}}
                        <div x-show="editModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" style="display: none;" x-cloak x-transition>
                            <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-8 text-left" @click.away="editModal = false">
                                <h3 class="text-xl font-bold text-slate-800 mb-6 tracking-tight italic uppercase tracking-widest">Edit Data Bahan</h3>
                                
                                <form action="{{ route('items.update', $item->id_bahanbaku) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                    @csrf 
                                    @method('PUT')
                                    
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Nama Bahan</label>
                                        <input type="text" name="nama_bahanbaku" value="{{ $item->nama_bahanbaku }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-semibold" required>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Satuan</label>
                                            <input type="text" name="satuan" value="{{ $item->satuan }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" required>
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Stok Minimum</label>
                                            <input type="number" step="any" name="stok_minimum" value="{{ $item->stok_minimum }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" required>
                                        </div>
                                    </div>

                                    {{-- INPUT HARGA (BARU) --}}
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Harga Satuan (Rata-rata)</label>
                                        <div class="relative">
                                            <span class="absolute left-3.5 top-3.5 text-slate-400 font-bold text-sm">Rp</span>
                                            <input type="number" name="harga" value="{{ $item->harga }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 pl-10 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-bold" placeholder="0">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="text-[10px] font-bold text-blue-600 uppercase block mb-1 tracking-widest">Update Stok Fisik (Gudang)</label>
                                        <input type="number" step="any" name="stok" value="{{ $item->stok }}" class="w-full bg-blue-50 border border-blue-100 p-3.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-bold text-lg" required>
                                        
                                        @if($item->stok_terkunci > 0)
                                            <div class="mt-2 p-2 bg-amber-50 border border-amber-100 rounded-lg">
                                                <p class="text-[10px] text-amber-700 font-bold leading-tight italic">
                                                    ⚠️ Ada {{ number_format($item->stok_terkunci, 2) }} {{ $item->satuan }} yang sedang dikunci produksi.
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2 tracking-widest text-center">Ganti Gambar</label>
                                        <div class="relative group">
                                            <input type="file" name="gambar_bahan" @change="editFileName = $event.target.files[0].name" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*">
                                            <div :class="editFileName ? 'border-blue-400 bg-blue-50' : 'border-slate-200'" class="border-2 border-dashed rounded-xl flex items-center justify-center p-6 transition-all group-hover:border-blue-400">
                                                <span class="text-xs font-black text-slate-500 uppercase tracking-widest" x-text="editFileName || 'Klik/Seret Gambar Baru'"></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="pt-4 flex justify-end space-x-3 border-t border-slate-50">
                                        <button type="button" @click="editModal = false" class="px-6 py-2 text-slate-400 font-black text-[10px] uppercase tracking-widest">Batal</button>
                                        <button type="submit" class="bg-blue-600 text-white px-10 py-3.5 rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        </div>
                    @else
                        <div class="flex justify-end">
                            <span class="px-3 py-1 bg-slate-100 text-slate-400 italic text-[10px] font-bold uppercase rounded-lg tracking-widest border border-slate-200">Read Only</span>
                        </div>
                    @endif
                </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center text-slate-400 font-medium">Bahan tidak ditemukan...</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL TAMBAH BAHAN BARU --}}
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" style="display: none;" x-cloak x-transition>
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-8 text-left" @click.away="openModal = false">
            <h3 class="text-xl font-bold text-slate-800 tracking-tight mb-8">Input Bahan Baru</h3>
            <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-2">Nama Bahan Baku</label>
                    <input type="text" name="nama_bahanbaku" class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 font-semibold" placeholder="Nama bahan..." required>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2 tracking-widest">Satuan</label>
                        <input type="text" name="satuan" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" placeholder="Roll/Kg" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2 tracking-widest">Stok Minimum</label>
                        <input type="number" step="0.01" name="stok_minimum" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" placeholder="5.00" required>
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-blue-600 uppercase block mb-2 tracking-widest text-center">Stok Awal</label>
                    <input type="number" step="0.01" name="stok" class="w-full bg-blue-50 border border-blue-100 p-4 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 font-bold text-center text-xl" placeholder="0.00" required>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-2 tracking-widest text-center">Gambar Bahan</label>
                    <div class="relative group">
                        <input type="file" name="gambar_bahan" @change="fileName = $event.target.files[0].name" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*">
                        <div :class="fileName ? 'border-blue-400 bg-blue-50' : 'border-slate-200'" class="border-2 border-dashed rounded-2xl flex flex-col items-center justify-center p-6 transition-all">
                            <span class="text-xs font-bold text-slate-500" x-text="fileName || 'Klik untuk Unggah Gambar'"></span>
                        </div>
                    </div>
                </div>
                <div class="pt-4 flex justify-end space-x-4 border-t border-slate-100 mt-6">
                    <button type="button" @click="openModal = false" class="px-6 py-2 text-slate-400 font-bold">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-10 py-3.5 rounded-2xl font-bold shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
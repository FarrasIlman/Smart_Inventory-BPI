@extends('layouts.main')

@section('page_title', 'Master Supplier')

@section('content')
<div x-data="{ openModal: false }">

    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h3 class="text-slate-800 font-bold text-xl tracking-tight">Master Supplier</h3>
            <p class="text-slate-400 text-xs mt-1">Kelola daftar vendor dan mitra pengadaan bahan</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <form action="{{ route('suppliers.index') }}" method="GET" class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari supplier..." class="w-full bg-white border border-slate-200 pl-10 pr-4 py-2.5 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </form>

            @if(auth()->user()->role == 'admin')
            <button @click="openModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-100 flex items-center justify-center shrink-0 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Tambah Supplier
            </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($suppliers as $supplier)
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-all relative overflow-hidden group" x-data="{ editModal: false }">
            
            <div class="absolute top-0 right-0">
                <span class="px-4 py-1.5 rounded-bl-2xl text-[10px] font-bold uppercase tracking-widest {{ $supplier->status_supplier == 'aktif' ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-400' }}">
                    {{ $supplier->status_supplier }}
                </span>
            </div>

            <div class="flex items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shrink-0 uppercase border border-blue-100">
                    {{ substr($supplier->nama_supplier, 0, 1) }}
                </div>
                <div class="ml-4 pr-10">
                    <h4 class="font-bold text-slate-800 leading-tight group-hover:text-blue-600 transition-colors">{{ $supplier->nama_supplier }}</h4>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1 tracking-wide">{{ $supplier->kode_supplier ?? 'Tanpa Kode' }}</p>
                </div>
            </div>

            <div class="space-y-2.5 mb-5">
                <div class="flex items-center text-xs text-slate-500">
                    <span class="w-16 text-[9px] font-bold text-slate-300 uppercase">PIC</span>
                    <span class="font-semibold text-slate-700">{{ $supplier->nama_pic ?? '-' }}</span>
                </div>
                <div class="flex items-center text-xs text-slate-500">
                    <span class="w-16 text-[9px] font-bold text-slate-300 uppercase">Telp</span>
                    <span class="text-slate-700 font-medium">{{ $supplier->no_telepon ?? '-' }}</span>
                </div>
                <div class="flex items-center text-xs text-slate-500">
                    <span class="w-16 text-[9px] font-bold text-slate-300 uppercase">Kota</span>
                    <span class="text-slate-700">{{ $supplier->kota ?? '-' }}</span>
                </div>
            </div>

            <div class="mb-6 p-3 bg-amber-50/50 rounded-2xl border border-dashed border-amber-200 relative group-hover:bg-amber-50 transition-colors">
                <p class="text-[9px] font-bold text-amber-500 uppercase mb-1 tracking-tighter flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Keterangan Supplier:
                </p>
                <p class="text-[11px] text-slate-600 leading-relaxed italic">
                    "{{ $supplier->keterangan ?? 'Belum ada keterangan.' }}"
                </p>
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-slate-50">
                <div class="flex gap-4">
                    <div class="text-left border-r border-slate-100 pr-4">
                        <p class="text-[9px] font-bold text-slate-300 uppercase italic leading-none mb-1">Lead Time</p>
                        <p class="text-xs font-bold text-slate-600">{{ $supplier->lead_time ?? 0 }} Hari</p>
                    </div>
                    <div class="text-left">
                        <p class="text-[9px] font-bold text-slate-300 uppercase italic leading-none mb-1">Min. Order</p>
                        <p class="text-xs font-bold text-slate-600">{{ $supplier->minimum_order ?? 0 }}</p>
                    </div>
                </div>
                
                @if(auth()->user()->role == 'admin')
                <div class="flex gap-1">
                    <button @click="editModal = true" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <form action="{{ route('suppliers.destroy', $supplier->id_supplier) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Hapus supplier ini?')" class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div x-show="editModal" class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" style="display: none;" x-cloak x-transition>
                <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl p-8 text-left max-h-[90vh] overflow-y-auto" @click.away="editModal = false">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-slate-800 tracking-tight">Edit Data Supplier</h3>
                        <button @click="editModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
                    </div>
                    <form action="{{ route('suppliers.update', $supplier->id_supplier) }}" method="POST" class="grid grid-cols-2 gap-5">
                        @csrf @method('PUT')
                        <div class="col-span-2 sm:col-span-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Nama Supplier</label>
                            <input type="text" name="nama_supplier" value="{{ $supplier->nama_supplier }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Kode Supplier</label>
                            <input type="text" name="kode_supplier" value="{{ $supplier->kode_supplier }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Nama PIC</label>
                            <input type="text" name="nama_pic" value="{{ $supplier->nama_pic }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">No. Telepon</label>
                            <input type="text" name="no_telepon" value="{{ $supplier->no_telepon }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Alamat Kantor/Gudang</label>
                            <textarea name="alamat" rows="2" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none">{{ $supplier->alamat }}</textarea>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Kota</label>
                            <input type="text" name="kota" value="{{ $supplier->kota }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Status Supplier</label>
                            <select name="status_supplier" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none cursor-pointer">
                                <option value="aktif" {{ $supplier->status_supplier == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak aktif" {{ $supplier->status_supplier == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Lead Time (Hari)</label>
                            <input type="number" name="lead_time" value="{{ $supplier->lead_time }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-blue-600 uppercase block mb-1 tracking-widest font-bold">Minimum Order</label>
                            <input type="number" name="minimum_order" value="{{ $supplier->minimum_order }}" class="w-full bg-blue-50 border border-blue-100 p-3.5 rounded-xl outline-none font-bold">
                        </div>
                        <div class="col-span-2">
                            <label class="text-[10px] font-bold text-amber-500 uppercase block mb-1 tracking-widest font-bold text-center">Catatan Internal / Keterangan</label>
                            <textarea name="keterangan" rows="2" class="w-full bg-amber-50/30 border border-amber-100 p-3.5 rounded-xl outline-none focus:ring-2 focus:ring-amber-500 text-sm italic" placeholder="Contoh: Fokus bahan cotton combed dan aksesoris.">{{ $supplier->keterangan }}</textarea>
                        </div>
                        <div class="col-span-2 flex justify-end gap-3 mt-6 border-t pt-6">
                            <button type="button" @click="editModal = false" class="px-6 py-2 text-slate-400 font-bold hover:text-slate-600 transition-colors">Batal</button>
                            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Update Supplier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <p class="text-slate-400 italic">Belum ada data supplier yang terdaftar.</p>
        </div>
        @endforelse
    </div>

    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" style="display: none;" x-cloak x-transition>
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl p-8 max-h-[90vh] overflow-y-auto" @click.away="openModal = false">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-xl font-bold text-slate-800 tracking-tight uppercase">Tambah Supplier Baru</h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 text-3xl font-light">&times;</button>
            </div>
            
            @if ($errors->any())
                <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl shadow-sm text-xs">
                    <p class="font-bold uppercase mb-1">Gagal Menyimpan:</p>
                    <ul class="list-disc ml-4">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                </div>
            @endif

            <form action="{{ route('suppliers.store') }}" method="POST" class="grid grid-cols-2 gap-5">
                @csrf
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Nama Perusahaan</label>
                    <input type="text" name="nama_supplier" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-semibold" required placeholder="PT. Nama Supplier">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Kode Supplier</label>
                    <input type="text" name="kode_supplier" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" placeholder="SUP-001">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Nama PIC</label>
                    <input type="text" name="nama_pic" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" placeholder="Bapak/Ibu Nama">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">No. Telepon</label>
                    <input type="text" name="no_telepon" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" placeholder="0812...">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Email Supplier</label>
                    <input type="email" name="email" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" placeholder="info@vendor.com">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" placeholder="Alamat kantor atau gudang..."></textarea>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Kota</label>
                    <input type="text" name="kota" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" placeholder="Bandung">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Status Supplier</label>
                    <select name="status_supplier" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none cursor-pointer">
                        <option value="aktif">Aktif</option>
                        <option value="tidak aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1 tracking-widest">Lead Time (Hari)</label>
                    <input type="number" name="lead_time" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-xl outline-none" placeholder="3">
                </div>
                <div>
                    <label class="text-[10px] font-bold text-blue-600 uppercase block mb-1 tracking-widest font-bold">Minimum Order</label>
                    <input type="number" name="minimum_order" class="w-full bg-blue-50 border border-blue-100 p-3.5 rounded-xl outline-none font-bold text-lg text-center" placeholder="10">
                </div>
                <div class="col-span-2">
                    <label class="text-[10px] font-bold text-amber-500 uppercase block mb-1 tracking-widest font-bold text-center">Catatan Internal / Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full bg-amber-50/30 border border-amber-100 p-3.5 rounded-xl outline-none focus:ring-2 focus:ring-amber-500 text-sm italic" placeholder="Contoh: Supplier Bahan Cotton dan Kancing"></textarea>
                </div>
                <div class="col-span-2 flex justify-end gap-3 mt-6 border-t pt-6">
                    <button type="button" @click="openModal = false" class="px-6 py-2 text-slate-400 font-bold hover:text-slate-600 transition-colors">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-10 py-3.5 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
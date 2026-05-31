@extends('layouts.main')
@section('page_title', 'Master BOM')
@section('content')
<div class="space-y-8" x-data="{ showAddModal: false, showEditModal: false, currentBom: {}, selectedProductId: null }">
    
    <div>
        <h1 class="text-2xl font-black text-slate-800">Bill of Materials (BOM)</h1>
        <p class="text-slate-500 text-xs mt-1">Kelola kebutuhan bahan baku (resep) untuk setiap produk.</p>
    </div>

    <div class="grid grid-cols-1 gap-8">
        @foreach($products as $p)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-black text-slate-700 uppercase text-sm italic">
                    {{ $p->nama_produk }} (ID Produk: #{{ $p->id_product }})
                </h3>
                <button @click="selectedProductId = {{ $p->id_product }}; showAddModal = true" 
                    class="bg-slate-900 text-white px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all">
                    + Tambah Bahan Baku
                </button>
            </div>
            
            <table class="w-full text-left text-sm">
                <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                    <tr>
                        <th class="px-6 py-4">Bahan Baku</th>
                        <th class="px-6 py-4 text-center">Jumlah Kebutuhan</th>
                        <th class="px-6 py-4 text-center">Waste (%)</th>
                        <th class="px-6 py-4 text-center">Total (Incl. Waste)</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($p->boms as $b)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-700 uppercase">{{ $b->rawMaterial->nama_bahanbaku ?? 'N/A' }}</p>
                        </td>
                        {{-- Data Angka Di-Center dan Satuan di sebelah angka --}}
                        <td class="px-6 py-4 text-center font-medium text-slate-600">
                            {{ $b->jumlah_kebutuhan }} <span class="text-[10px] text-slate-400 font-normal ml-1">{{ $b->rawMaterial->satuan ?? '' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center text-orange-500 font-bold">
                            {{ $b->persentase_waste }}%
                        </td>
                        <td class="px-6 py-4 text-center font-black text-slate-800">
                            {{ number_format($b->jumlah_kebutuhan * (1 + $b->persentase_waste/100), 2) }} 
                            <span class="text-[10px] text-slate-400 font-normal ml-1">{{ $b->rawMaterial->satuan ?? '' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center gap-2">
                                <button @click="currentBom = {{ json_encode($b) }}; showEditModal = true" 
                                    class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2.5"/></svg>
                                </button>
                                <form action="{{ route('bom.destroy', $b->id_bom) }}" method="POST" onsubmit="return confirm('Hapus bahan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic text-xs">Belum ada bahan baku.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endforeach
    </div>

    {{-- MODAL TAMBAH --}}
    <div x-show="showAddModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak x-transition>
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showAddModal = false"></div>
        
        <form action="{{ route('bom.store') }}" method="POST" class="bg-white w-full max-w-md rounded-2xl shadow-xl relative z-10 overflow-hidden">
            @csrf
            
            {{-- POIN KRUSIAL: Pastikan id_product terisi otomatis dari Alpine.js --}}
            <input type="hidden" name="id_product" :value="selectedProductId">

            <div class="p-6 border-b border-slate-100 bg-slate-50">
                <h3 class="text-lg font-bold text-slate-800 uppercase italic">Tambah Bahan Baku</h3>
            </div>

            <div class="p-8 space-y-4">
                {{-- Pilih Bahan --}}
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Pilih Bahan</label>
                    <select name="id_bahanbaku" required class="w-full border border-slate-200 rounded-xl px-3 py-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">Pilih...</option>
                        @foreach($materials as $mat)
                            <option value="{{ $mat->id_bahanbaku }}">{{ $mat->nama_bahanbaku }} ({{ $mat->satuan }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Qty Kebutuhan</label>
                        <input type="number" step="0.01" name="jumlah_kebutuhan" required class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Waste (%)</label>
                        <input type="number" step="0.01" name="persentase_waste" required class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="p-6 bg-slate-50 flex justify-end gap-3 border-t border-slate-100">
                <button type="button" @click="showAddModal = false" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-red-500">Batal</button>
                <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-blue-100">
                    Simpan Bahan
                </button>
            </div>
        </form>
    </div>

    {{-- MODAL EDIT --}}
    <div x-show="showEditModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak x-transition>
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showEditModal = false"></div>
        <form :action="`/bom/${currentBom.id_bom}`" method="POST" class="bg-white w-full max-w-md rounded-2xl shadow-xl relative z-10 overflow-hidden">
            @csrf @method('PUT')
            <div class="p-6 border-b border-slate-100 bg-slate-50"><h3 class="text-lg font-bold text-slate-800 uppercase italic">Edit Kebutuhan</h3></div>
            <div class="p-8 space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Nama Bahan</label>
                    <input type="text" :value="currentBom.rawMaterial?.nama_bahanbaku" disabled class="w-full bg-slate-100 border-none rounded-xl px-4 py-3 text-sm text-slate-500 font-bold uppercase">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Qty Kebutuhan</label>
                        <input type="number" step="0.01" name="jumlah_kebutuhan" x-model="currentBom.jumlah_kebutuhan" required class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Waste (%)</label>
                        <input type="number" step="0.01" name="persentase_waste" x-model="currentBom.persentase_waste" required class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <div class="p-6 bg-slate-50 flex justify-end gap-3">
                <button type="button" @click="showEditModal = false" class="text-xs font-bold text-slate-400 uppercase">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg text-xs font-black uppercase hover:bg-slate-900 transition-all">Simpan Perubahan</button>
            </div>
        </form>
    </div>

</div>
@endsection
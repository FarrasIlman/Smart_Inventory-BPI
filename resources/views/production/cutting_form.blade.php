@extends('layouts.main')

@section('page_title', 'Form Potong Produksi')

@section('content')
<div class="space-y-8 pb-20">
    
    {{-- Header Dokumen --}}
    <div class="flex justify-between items-center pb-4 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center text-xl font-black italic">BP</div>
            <div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Form Potong</h1>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-0.5">Bumiputera Persada Industri • Produksi</p>
            </div>
        </div>
        <a href="{{ route('production.index', $order->id_order) }}" class="bg-white border border-slate-200 text-slate-600 px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
            ← Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 px-6 py-4 rounded-2xl text-sm font-bold shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM UTAMA UNTUK UPDATE DATA POTONG --}}
    <form action="{{ route('production.updateCuttingForm', $order->id_order) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Identitas Potong --}}
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-sm space-y-6">
                <h3 class="text-slate-800 font-black text-sm uppercase tracking-widest italic mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-blue-600 rounded-full"></span> Informasi Form Potong (Dapat Diedit)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">No. Produksi</span>
                        <p class="font-black text-slate-800 text-base py-2">#ORD-{{ $order->id_order }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Tanggal Rilis Order</span>
                        <p class="font-bold text-slate-700 py-2">{{ \Carbon\Carbon::parse($order->tanggal_pesan)->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Konsumen / Pelanggan</span>
                        <p class="font-black text-slate-800 uppercase text-base tracking-tight py-2">{{ $order->nama_pelanggan }}</p>
                    </div>
                    
                    {{-- EDITABLE: Warna Artikel --}}
                    <div>
                        <label class="text-[10px] font-black text-blue-600 uppercase tracking-wider block mb-1">Warna Artikel Kain</label>
                        <input type="text" name="warna_artikel" value="{{ old('warna_artikel', $order->production->warna_artikel ?? 'Biru') }}" 
                            class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>

                    <div class="col-span-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block mb-1">Nama Item / Produk</span>
                        <p class="font-black text-blue-600 uppercase text-base leading-tight py-2">{{ $order->product->nama_produk }}</p>
                    </div>

                    {{-- EDITABLE: Model Potongan --}}
                    <div>
                        <label class="text-[10px] font-black text-blue-600 uppercase tracking-wider block mb-1">Model Potongan Pola</label>
                        <input type="text" name="model_potongan" value="{{ old('model_potongan', $order->production->model_potongan ?? 'Standar Pola Konveksi BP') }}" 
                            class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>

                    {{-- EDITABLE: Petugas Operator --}}
                    <div>
                        <label class="text-[10px] font-black text-blue-600 uppercase tracking-wider block mb-1">Petugas Operator Potong</label>
                        <input type="text" name="petugas" value="{{ old('petugas', $order->production->petugas ?? auth()->user()->nama_user) }}" 
                            class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>

                    {{-- BARU & EDITABLE: Tanggal Deadline Target Potong --}}
                    <div class="col-span-2">
                        <label class="text-[10px] font-black text-red-500 uppercase tracking-wider block mb-1">Target Deadline Selesai Potong</label>
                        <input type="date" name="deadline_potong" value="{{ old('deadline_potong', $order->production->deadline_potong ?? $order->deadline) }}" 
                            class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm text-red-600 outline-none focus:ring-2 focus:ring-red-400 transition-all">
                        <p class="text-[10px] text-slate-400 italic mt-1">Batas akhir pengerjaan meja potong sebelum naik ke tahap branding/jahit. (Default: Tanggal deadline order utama)</p>
                    </div>
                </div>
            </div>

            {{-- Preview Gambar Desain Samping --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-sm flex flex-col">
                <h3 class="text-slate-800 font-black text-sm uppercase tracking-widest italic mb-4">Desain Produk Pesanan</h3>
                <div class="flex-1 aspect-square bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden p-4">
                    @if($order->gambar_desain)
                        <img src="{{ asset('storage/' . $order->gambar_desain) }}" class="w-full h-full object-contain">
                    @else
                        <div class="text-slate-300 text-center">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <p class="text-[10px] font-black uppercase tracking-widest">Sampel Gambar Kosong</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kebutuhan Komponen Kain & Aksesoris --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-sm">
            <h3 class="text-slate-800 font-black text-sm uppercase tracking-widest italic mb-6 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-emerald-500 rounded-full"></span> Alokasi & Kebutuhan Bahan Baku Berjalan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($order->product->boms as $bom)
                    @php
                        $total_butuh = $bom->jumlah_kebutuhan * $order->jumlah_pesanan * (1 + $bom->persentase_waste/100);
                    @endphp
                    <div class="p-5 bg-emerald-50/40 border border-emerald-100 rounded-2xl flex flex-col justify-between">
                        <p class="text-[10px] font-black text-emerald-700 uppercase tracking-wider mb-2">{{ $bom->rawMaterial->nama_bahanbaku ?? 'Material' }}</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-xl font-black text-slate-800 tracking-tight">{{ number_format($total_butuh, 2) }}</span>
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $bom->rawMaterial->satuan ?? '' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-4 text-center text-slate-400 italic text-xs">Resep komponen bahan baku belum di-link ke dalam master produk ini.</div>
                @endforelse
            </div>
        </div>

        {{-- Tabel Distribusi Ukuran Potong --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 px-8 bg-slate-50/50 border-b border-slate-100">
                <h3 class="text-slate-800 font-black text-sm uppercase tracking-widest italic">Breakdown Distribusi Ukuran Potong</h3>
            </div>
            <table class="w-full text-left text-sm font-bold">
                <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/30 border-b border-slate-100">
                    <tr>
                        <th class="px-8 py-4 w-24 text-center">No</th>
                        <th class="px-6 py-4 text-center">Size / Ukuran</th>
                        <th class="px-6 py-4 text-center">Spesifikasi Model</th>
                        <th class="px-8 py-4 text-right">Target Potong (Qty Pcs)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700 text-xs">
                    @foreach($order->details as $detail)
                    <tr>
                        <td class="px-8 py-4 text-center font-mono text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-black uppercase">{{ $detail->size }}</span>
                        </td>
                        <td class="px-6 py-4 text-center text-slate-400 font-medium italic">Sesuai Form Pola Pola Panjang</td>
                        <td class="px-8 py-4 text-right text-base font-black text-slate-900">{{ $detail->quantity }} Pcs</td>
                    </tr>
                    @endforeach
                    <tr class="bg-slate-50/50 font-black text-slate-800">
                        <td colspan="3" class="px-8 py-5 text-right uppercase tracking-wider text-xs text-slate-400">Total Akumulasi Potong:</td>
                        <td class="px-8 py-5 text-right text-lg font-black text-blue-600">{{ $order->jumlah_pesanan }} Pcs</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- EDITABLE: Lembar Catatan Kerja Meja Potong --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-sm">
            <h3 class="text-slate-800 font-black text-sm uppercase tracking-widest italic mb-3 flex items-center gap-2">
                <span class="w-1.5 h-4 bg-amber-500 rounded-full"></span> Catatan Meja Potong / Kelonggaran Kain
            </h3>
            <textarea name="catatan_potong" rows="5" placeholder="Masukkan instruksi pengerjaan meja potong di sini (Contoh: Potongan bahan disisakan 2cm untuk lipatan keliman jahit, potong kain searah serat benang, dsb)..." 
                class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl text-xs font-semibold outline-none focus:ring-2 focus:ring-blue-500 transition-all shadow-inner">{{ old('catatan_potong', $order->production->catatan_potong ?? '') }}</textarea>
        </div>

        {{-- SUBMIT BUTTON BAR --}}
        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-slate-950 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-xl shadow-blue-100 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Simpan & Update Form Potong
            </button>
        </div>

    </form>
</div>
@endsection
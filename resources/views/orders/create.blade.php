@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ 
    details: [{size: 'S', qty: 0}],
    totalQty() {
        return this.details.reduce((sum, item) => sum + (parseInt(item.qty) || 0), 0);
    }
}">
    <div class="flex items-center gap-4">
        <a href="{{ route('orders.index') }}" class="p-2.5 bg-slate-100 text-slate-400 hover:text-red-500 rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2"/>
            </svg>
        </a>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Tambah Pesanan Baru</h1>
    </div>

    <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8 space-y-8">
            
            {{-- SECTION: CUSTOMER --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Produk</label>
                    <select name="id_product" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id_product }}">{{ $product->nama_produk }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">No Telepon</label>
                    <input type="text" name="no_telepon" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Alamat</label>
                    <textarea name="alamat" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm"></textarea>
                </div>
            </div>

            {{-- SECTION: ORDER --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Deadline Produksi</label>
                    <input type="date" name="deadline" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Kuantitas (Pcs)</label>
                    <input type="number" name="jumlah_pesanan" :value="totalQty()" readonly class="w-full bg-blue-50 border border-blue-100 text-blue-600 font-bold rounded-2xl px-5 py-3 text-sm">
                </div>
            </div>

            {{-- TAMBAHAN HARGA --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Harga Satuan</label>
                <input type="number" name="harga_satuan" required class="w-full bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-3 text-sm">
            </div>

            <hr class="border-slate-50">

            <div x-data="imageViewer()">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                    Upload Desain
                </label>

                <div class="flex items-center gap-6">
                    
                    <!-- PREVIEW BOX -->
                    <div class="w-32 h-32 bg-slate-100 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden">
                        
                        <!-- kalau ada gambar -->
                        <template x-if="imageUrl">
                            <img :src="imageUrl" class="w-full h-full object-contain">
                        </template>

                        <!-- kalau belum ada -->
                        <template x-if="!imageUrl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-width="1.5" d="M3 16l4-4a3 3 0 014.24 0l4 4M14 14l1-1a3 3 0 014.24 0L21 15M14 10h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </template>

                    </div>

                    <!-- INPUT FILE -->
                    <div class="flex-1">
                        <input type="file" name="gambar_desain" @change="fileChosen"
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                    </div>

                </div>
            </div>

            <hr class="border-slate-50">

            {{-- SIZE --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Rincian Ukuran & Jumlah</label>

                <div class="space-y-3">
                    <template x-for="(detail, index) in details" :key="index">
                        <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            
                            <select x-model="detail.size" name="sizes[]" class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm">
                                <option>S</option>
                                <option>M</option>
                                <option>L</option>
                                <option>XL</option>
                                <option>XXL</option>
                            </select>

                            <input type="number" x-model="detail.qty" name="quantities[]" class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm">

                            <button type="button" @click="details.splice(index,1)" class="text-red-500">
                                ✕
                            </button>

                        </div>
                    </template>
                </div>

                <button type="button" @click="details.push({size:'S', qty:0})" class="text-blue-500 text-sm mt-2">
                    + Tambah Baris Ukuran
                </button>
            </div>

        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-bold">
                Simpan Pesanan
            </button>
        </div>
    </form>
</div>

<script>
function imageViewer() {
    return {
        imageUrl: '',
        fileChosen(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => this.imageUrl = e.target.result;
            reader.readAsDataURL(file);
        }
    }
}
</script>
@endsection
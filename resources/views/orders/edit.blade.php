@extends('layouts.main')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ 
    details: {{ $order->details->map(fn($d) => ['size' => $d->size, 'qty' => $d->quantity])->toJson() }} 
}">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}" class="group flex items-center justify-center p-2.5 bg-slate-100 hover:bg-red-50 text-slate-400 hover:text-red-500 rounded-xl transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">
                Edit Pesanan #{{ $order->id_order }}
            </h1>
        </div>

        <div class="flex md:justify-end">
            <button form="editOrderForm" type="submit"
                class="w-full md:w-auto bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all uppercase text-xs tracking-widest">
                Simpan Perubahan
            </button>
        </div>
    </div>

    <form id="editOrderForm" action="{{ route('orders.update', $order->id_order) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-8 space-y-8">

            <div class="space-y-6 p-6 bg-slate-50/50 rounded-2xl border border-slate-100">
                <h3 class="text-slate-800 font-bold text-base mb-4 italic text-blue-600">
                    Informasi Utama Pesanan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Nama Pelanggan
                        </label>
                        <input type="text" name="nama_pelanggan"
                            value="{{ old('nama_pelanggan', $order->nama_pelanggan) }}"
                            required
                            class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Pilih Produk
                        </label>
                        <select name="id_product" required
                            class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                            @foreach($products as $product)
                                <option value="{{ $product->id_product }}"
                                    {{ old('id_product', $order->id_product) == $product->id_product ? 'selected' : '' }}>
                                    {{ $product->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                        No Telepon
                    </label>
                    <input type="text" name="no_telepon"
                        value="{{ old('no_telepon', $order->no_telepon) }}"
                        class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                        Alamat
                    </label>
                    <textarea name="alamat"
                        class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3 text-sm">{{ old('alamat', $order->alamat) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Total Kuantitas (Pcs)
                        </label>
                        <input type="number" name="jumlah_pesanan"
                            value="{{ old('jumlah_pesanan', $order->jumlah_pesanan) }}"
                            required
                            class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Deadline
                        </label>
                        <input type="date" name="deadline"
                            value="{{ old('deadline', $order->deadline) }}"
                            required
                            class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            Status
                        </label>
                        <select name="status_order"
                            class="w-full bg-white border border-slate-200 rounded-2xl px-5 py-3 text-sm">
                            <option value="menunggu bahan" {{ old('status_order', $order->status_order) == 'menunggu bahan' ? 'selected' : '' }}>Menunggu Bahan</option>
                            <option value="siap produksi" {{ old('status_order', $order->status_order) == 'siap produksi' ? 'selected' : '' }}>Siap Produksi</option>
                            <option value="produksi" {{ old('status_order', $order->status_order) == 'produksi' ? 'selected' : '' }}>Dalam Produksi</option>
                            <option value="selesai" {{ $order->status_order == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                        Harga Satuan
                    </label>
                    <input type="number" name="harga_satuan"
                        value="{{ old('harga_satuan', $order->harga_satuan) }}"
                        class="w-full bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-3 text-sm">
                </div>

            </div>

            <hr class="border-slate-50">

            <div class="space-y-4" x-data="imagePreviewHandler()">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1 italic">Gambar Desain</label>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center bg-slate-50/50 p-6 rounded-3xl border border-slate-100">
                    
                    <div class="aspect-square bg-white rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden relative group">
                        
                        <template x-if="imageUrl">
                            <img :src="imageUrl" class="w-full h-full object-contain p-4 transition-transform duration-300 hover:scale-105">
                        </template>

                        <template x-if="!imageUrl && '{{ $order->gambar_desain }}'">
                            <img src="{{ asset('storage/' . $order->gambar_desain) }}" class="w-full h-full object-contain p-4 transition-transform duration-300 hover:scale-105">
                        </template>

                        <template x-if="!imageUrl && !'{{ $order->gambar_desain }}'">
                            <div class="text-center p-6 text-slate-300">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                <p class="text-xs font-bold uppercase tracking-widest">Belum Ada Desain</p>
                            </div>
                        </template>

                        <div x-show="imageUrl" class="absolute top-3 left-3" x-cloak>
                            <span class="text-[10px] px-2 py-1 bg-amber-500 text-white font-black rounded-full uppercase">Pratinjau Baru</span>
                        </div>
                    </div>
                    
                    <div 
                        class="relative group h-full"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="handleDrop($event)"
                    >
                        <input 
                            type="file" 
                            name="gambar_desain" 
                            id="gambar_desain" 
                            accept="image/*" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            @change="fileChosen($event)"
                        >
                        
                        <div 
                            class="p-6 h-full bg-white border-2 border-dashed rounded-3xl transition-colors flex flex-col items-center justify-center text-center space-y-3"
                            :class="isDragging ? 'border-blue-500 bg-blue-50' : 'border-slate-200 group-hover:border-blue-400'"
                        >
                            <div class="w-12 h-12 rounded-full flex items-center justify-center transition-colors"
                                :class="isDragging ? 'bg-blue-100 text-blue-700' : 'bg-blue-50 text-blue-600'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            
                            <div class="space-y-1">
                                <p class="text-sm font-semibold text-slate-800" x-text="isDragging ? 'Lepaskan Gambar di Sini' : 'Seret & Lepas atau Klik'"></p>
                                <p class="text-xs text-slate-400">Untuk Unggah Desain Tambahan (Max 10MB, .jpg, .png)</p>
                            </div>

                            <button 
                                type="button" 
                                x-show="imageUrl" 
                                @click="resetImage()" 
                                class="relative z-20 px-4 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100 transition-colors"
                                x-cloak
                            >
                                Batal Pilih Gambar Baru
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Rincian Ukuran Dinamis --}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 px-1 italic">Rincian Ukuran & Jumlah</label>
                
                <div class="space-y-3">
                    <template x-for="(detail, index) in details" :key="index">
                        <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100 hover:border-blue-200 transition-colors">
                            <div class="flex-1">
                                <select x-model="detail.size" name="sizes[]" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs outline-none cursor-pointer">
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="XXL">XXL</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <input type="number" x-model="detail.qty" name="quantities[]" placeholder="Qty" required min="1" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-xs outline-none">
                            </div>
                            <button type="button" @click="details.splice(index, 1)" class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="details.push({size: 'S', qty: 1})" class="mt-4 flex items-center gap-1.5 text-blue-600 font-bold text-xs hover:underline uppercase tracking-wider">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Tambah Varian Ukuran
                </button>
            </div>
        </div>

        {{-- Tombol Simpan Perubahan di Bawah --}}
        <div class="flex justify-end pt-4 pb-8">
            <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-10 py-4 rounded-3xl font-black text-sm shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all uppercase tracking-widest">
                Simpan Perubahan
            </button>
        </div>
    </form>
    <script>
    // Logic Alpine.js untuk gambar dan drag-and-drop
        function imagePreviewHandler() {
            return {
                imageUrl: null,
                isDragging: false,

                // Menangani ketika file dipilih melalui klik (input biasa)
                fileChosen(event) {
                    this.fileToUrl(event.target.files[0]);
                },

                // Menangani ketika file dijatuhkan (drop) di kotak desain
                handleDrop(event) {
                    this.isDragging = false;
                    const file = event.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        document.getElementById('gambar_desain').files = event.dataTransfer.files;
                        this.fileToUrl(file);
                    } else {
                        alert('Harap jatuhkan file gambar (.jpg, .png)');
                    }
                },

                // Mengonversi file mentah menjadi URL pratinjau sementara
                fileToUrl(file) {
                    if (!file) return;
                    
                    // Gunakan FileReader API bawaan browser
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = (e) => {
                        this.imageUrl = e.target.result;
                    };
                },

                // Menghapus pratinjau baru dan kembali ke gambar lama
                resetImage() {
                    this.imageUrl = null;
                    document.getElementById('gambar_desain').value = '';
                }
            }
        }
    </script>
</div>
@endsection
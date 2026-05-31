@extends('layouts.main')

@section('content')
<div class="max-w-6xl mx-auto space-y-6"
    x-data="{
        items: [{ material:'', qty:1, harga:0, satuan:'' }],

        addRow() {
            this.items.push({ material:'', qty:1, harga:0, satuan:'' });
        },

        removeRow(i) {
            this.items.splice(i,1);
        },

        subtotal(item) {
            return item.qty * item.harga;
        },

        formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka || 0);
        }
    }"
>

    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('purchases.index') }}"
                class="p-2.5 bg-slate-100 text-slate-400 hover:text-blue-600 rounded-xl">
                ←
            </a>
            <h1 class="text-2xl font-black text-slate-800">Tambah Pembelian</h1>
        </div>
    </div>
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 px-5 py-3 rounded-xl text-sm font-semibold">
            <ul class="list-disc ml-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 px-5 py-3 rounded-xl text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif
    <!-- FORM -->
    <form action="{{ route('purchases.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-[32px] border border-slate-100 p-8 space-y-8">

            <!-- SUPPLIER -->
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">
                    Supplier
                </label>

                <select name="id_supplier">
                    <option value="">-- Pilih Supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id_supplier }}"
                            {{ old('id_supplier') == $s->id_supplier ? 'selected' : '' }}>
                            {{ $s->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>

            <hr class="border-slate-100">

            <!-- DAFTAR BAHAN -->
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-4">
                    Daftar Bahan
                </label>

                <!-- HEADER TABLE -->
                <div class="grid grid-cols-12 gap-4 mb-3 px-2">
                    <p class="col-span-4 text-xs font-bold text-slate-400">Bahan</p>
                    <p class="col-span-1 text-xs font-bold text-slate-400 text-center">Qty</p>
                    <p class="col-span-2 text-xs font-bold text-slate-400 text-center">Satuan</p>
                    <p class="col-span-2 text-xs font-bold text-slate-400 text-center">Harga / Unit</p>
                    <p class="col-span-2 text-xs font-bold text-slate-400 text-center">Subtotal</p>
                    <p class="col-span-1 text-xs font-bold text-slate-400 text-center">Aksi</p>
                </div>

                <!-- ROW -->
                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-4 items-center bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 mb-3">

                        <!-- BAHAN -->
                        <select :name="'materials['+index+']'"
                            x-model="item.material"
                            @change="item.satuan = $event.target.options[$event.target.selectedIndex].dataset.satuan || item.satuan"
                            class="col-span-4 bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm">

                            <option value="">Pilih Bahan Baku</option>

                            @foreach($materials as $m)
                                <option value="{{ $m->id_bahanbaku }}"
                                        data-satuan="{{ $m->satuan }}">
                                    {{ $m->nama_bahanbaku }}
                                </option>
                            @endforeach
                        </select>

                        <!-- QTY -->
                        <input type="number" step="0.01"
                            :name="'jumlah['+index+']'"
                            x-model="item.qty"
                            class="col-span-1 bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-center">

                        <!-- SATUAN (DROPDOWN) -->
                        <select :name="'satuan['+index+']'"
                            x-model="item.satuan"
                            class="col-span-2 bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-center">

                            <option value="">Pilih</option>
                            <option value="Kg">Kg</option>
                            <option value="Gram">Gram</option>
                            <option value="Roll">Roll</option>
                            <option value="Meter">Meter</option>
                            <option value="Pcs">Pcs</option>
                        </select>

                        <!-- HARGA -->
                        <input type="number" step="0.01"
                            :name="'harga['+index+']'"
                            x-model="item.harga"
                            class="col-span-2 bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm text-center">

                        <!-- SUBTOTAL -->
                        <p class="col-span-2 text-sm font-bold text-green-600 text-center">
                            Rp <span x-text="formatRupiah(subtotal(item))"></span>
                        </p>

                        <!-- HAPUS -->
                        <button type="button"
                            @click="removeRow(index)"
                            class="col-span-1 text-red-500 text-sm font-bold text-center hover:underline">
                            Hapus
                        </button>

                    </div>
                </template>

                <!-- TAMBAH -->
                <button type="button" @click="addRow()"
                    class="text-blue-600 text-sm font-bold mt-2">
                    + Tambah Bahan Baku
                </button>

            </div>

        </div>

        <!-- SUBMIT -->
        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl font-bold shadow-lg">
                Simpan Pembelian
            </button>
        </div>

    </form>
</div>
@endsection
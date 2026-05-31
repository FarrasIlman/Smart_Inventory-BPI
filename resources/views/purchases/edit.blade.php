@extends('layouts.main')

@section('content')
<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-black text-slate-800">Edit Pembelian</h1>

        <a href="{{ route('purchases.show', $purchase->id_purchase) }}"
           class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm font-bold hover:bg-slate-50">
            ← Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('purchases.update', $purchase->id_purchase) }}">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-[32px] border border-slate-100 p-8 space-y-8">

            <div class="grid grid-cols-2 gap-6">
                <!-- SUPPLIER -->
                <div>
                    <p class="text-xs text-slate-400 font-bold mb-2">Supplier</p>
                    <select name="id_supplier"
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm">
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id_supplier }}"
                                {{ $purchase->id_supplier == $s->id_supplier ? 'selected' : '' }}>
                                {{ $s->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- STATUS -->
                <div>
                    <p class="text-xs text-slate-400 font-bold mb-2">Status Pembelian</p>

                    <select name="status_pembelian"
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm font-semibold">

                        <option value="dipesan"
                            {{ $purchase->status_pembelian == 'dipesan' ? 'selected' : '' }}>
                            🟡 Dipesan
                        </option>

                        <option value="diterima"
                            {{ $purchase->status_pembelian == 'diterima' ? 'selected' : '' }}>
                            🟢 Diterima
                        </option>

                        <option value="dikembalikan"
                            {{ $purchase->status_pembelian == 'dikembalikan' ? 'selected' : '' }}>
                            🔴 Dikembalikan
                        </option>

                    </select>
                </div>

            </div>

            <!-- DAFTAR BAHAN -->
            <div>
                <p class="text-xs text-slate-400 font-bold mb-4">Daftar Bahan</p>

                <!-- HEADER KOLOM -->
                <div class="grid grid-cols-6 gap-3 mb-2 px-2">
                    <p class="text-[11px] font-bold text-slate-400">Bahan</p>
                    <p class="text-[11px] font-bold text-slate-400 text-center">Qty</p>
                    <p class="text-[11px] font-bold text-slate-400 text-center">Satuan</p>
                    <p class="text-[11px] font-bold text-slate-400 text-center">Harga / Unit</p>
                    <p class="text-[11px] font-bold text-slate-400 text-right">Subtotal</p>
                    <p class="text-[11px] font-bold text-slate-400 text-center">Aksi</p>
                </div>

                <!-- ROWS -->
                <div id="rows" class="space-y-3">

                    @foreach($purchase->details as $d)
                    <div class="grid grid-cols-6 gap-3 items-center bg-slate-50 border border-slate-100 rounded-xl p-3">

                        <!-- Bahan -->
                        <select name="materials[]"
                            class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm">
                            @foreach($materials as $m)
                                <option value="{{ $m->id_bahanbaku }}"
                                    {{ $d->id_bahanbaku == $m->id_bahanbaku ? 'selected' : '' }}>
                                    {{ $m->nama_bahanbaku }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Qty -->
                        <input type="number" step="0.01" name="jumlah[]"
                            value="{{ $d->jumlah }}"
                            class="text-center bg-white border border-slate-200 rounded-lg px-2 py-2 text-sm qty">

                        <!-- Satuan -->
                        <select name="satuan[]"
                            class="text-center bg-white border border-slate-200 rounded-lg px-2 py-2 text-sm">
                            
                            <option value="kg" {{ $d->material->satuan == 'kg' ? 'selected' : '' }}>Kg</option>
                            <option value="meter" {{ $d->material->satuan == 'meter' ? 'selected' : '' }}>Meter</option>
                            <option value="pcs" {{ $d->material->satuan == 'pcs' ? 'selected' : '' }}>Pcs</option>
                            <option value="roll" {{ $d->material->satuan == 'roll' ? 'selected' : '' }}>Roll</option>

                        </select>

                        <!-- Harga -->
                        <input type="number" step="0.01" name="harga[]"
                            value="{{ $d->harga }}"
                            class="text-center bg-white border border-slate-200 rounded-lg px-2 py-2 text-sm harga">

                        <!-- Subtotal -->
                        <div class="text-right font-bold text-green-600 text-sm subtotal">
                            Rp {{ number_format($d->jumlah * $d->harga,0,',','.') }}
                        </div>

                        <!-- Hapus -->
                        <div class="text-center">
                            <button type="button"
                                onclick="removeRow(this)"
                                class="text-red-500 hover:text-red-600 text-xs font-bold">
                                Hapus
                            </button>
                        </div>

                    </div>
                    @endforeach

                </div>

                <!-- ADD -->
                <button type="button" onclick="addRow()"
                    class="text-blue-600 hover:text-blue-700 text-sm font-bold mt-4">
                    + Tambah Bahan
                </button>
            </div>

            <!-- SUBMIT -->
            <div class="flex justify-end">
                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow">
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </form>
</div>

<script>
function removeRow(btn){
    btn.closest('.grid').remove();
}

function addRow(){
    const html = `
    <div class="grid grid-cols-6 gap-3 items-center bg-slate-50 border border-slate-100 rounded-xl p-3">

        <select name="materials[]" class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm">
            <option value="">Pilih Bahan</option>
            @foreach($materials as $m)
                <option value="{{ $m->id_bahanbaku }}">{{ $m->nama_bahanbaku }}</option>
            @endforeach
        </select>

        <input type="number" step="0.01" name="jumlah[]"
            class="qty text-center bg-white border border-slate-200 rounded-lg px-2 py-2 text-sm">

        <select name="satuan[]"
            class="text-center bg-white border border-slate-200 rounded-lg px-2 py-2 text-sm">
            <option value="kg">Kg</option>
            <option value="meter">Meter</option>
            <option value="pcs">Pcs</option>
        </select>

        <input type="number" step="0.01" name="harga[]"
            class="harga text-center bg-white border border-slate-200 rounded-lg px-2 py-2 text-sm">

        <div class="text-right font-bold text-green-600 text-sm subtotal">Rp 0</div>

        <div class="text-center">
            <button type="button" onclick="removeRow(this)"
                class="text-red-500 text-xs font-bold">Hapus</button>
        </div>
    </div>
    `;

    const container = document.getElementById('rows');
    container.insertAdjacentHTML('beforeend', html);

    // re-init untuk row baru
    initCalculation();
}

function formatRupiah(angka){
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
}

function updateSubtotal(row){
    const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
    const harga = parseFloat(row.querySelector('.harga')?.value) || 0;

    const subtotal = qty * harga;

    row.querySelector('.subtotal').innerText = formatRupiah(subtotal);
}

function initCalculation(){
    document.querySelectorAll('#rows > div').forEach(row => {

        const qtyInput = row.querySelector('.qty');
        const hargaInput = row.querySelector('.harga');

        if(qtyInput){
            qtyInput.addEventListener('input', () => updateSubtotal(row));
        }

        if(hargaInput){
            hargaInput.addEventListener('input', () => updateSubtotal(row));
        }
        updateSubtotal(row);
    });
}

function updateTotal(){
    let total = 0;

    document.querySelectorAll('.subtotal').forEach(el => {
        const value = el.innerText.replace(/[^\d]/g,'');
        total += parseInt(value || 0);
    });

    document.getElementById('grandTotal').innerText = formatRupiah(total);
}

document.addEventListener('DOMContentLoaded', initCalculation);
</script>
@endsection
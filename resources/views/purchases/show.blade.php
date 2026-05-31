@extends('layouts.main')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-600 px-6 py-4 rounded-2xl text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif
    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-800">
                    Detail Pembelian
                </h1>

                <!-- ID -->
                <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-500 text-sm font-bold">
                    #{{ $purchase->id_purchase }}
                </span>
            </div>

            <p class="text-slate-400 text-xs mt-1">
                Informasi lengkap transaksi pembelian bahan
            </p>
        </div>

        <a href="{{ route('purchases.index') }}"
            class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-sm font-bold">
            ← Kembali
        </a>
    </div>

    <!-- INFO -->
    <div class="bg-white rounded-[32px] border border-slate-100 p-8 grid grid-cols-4 gap-6">
    <!-- Supplier -->
        <div>
            <p class="text-xs text-slate-400 font-bold">Supplier</p>
            <p class="font-bold text-slate-800">
                {{ $purchase->supplier->nama_supplier ?? '-' }}
            </p>
        </div>

        <!-- Tanggal -->
        <div>
            <p class="text-xs text-slate-400 font-bold">Tanggal</p>
            <p class="font-bold text-slate-800">
                {{ \Carbon\Carbon::parse($purchase->tanggal_pembelian)->format('d/m/Y') }}
            </p>
        </div>

        <!-- Status -->
        <div>
            <p class="text-xs text-slate-400 font-bold">Status</p>

            <span class="inline-block mt-1 px-3 py-1.5 rounded-full text-xs font-bold
                {{ $purchase->status_pembelian == 'dipesan' ? 'bg-amber-50 text-amber-600 border border-amber-100' : '' }}
                {{ $purchase->status_pembelian == 'diterima' ? 'bg-green-50 text-green-600 border border-green-100' : '' }}
                {{ $purchase->status_pembelian == 'dikembalikan' ? 'bg-red-50 text-red-600 border border-red-100' : '' }}">
                {{ ucfirst($purchase->status_pembelian) }}
            </span>
        </div>

        <!-- Aksi -->
        <div>
            <p class="text-xs text-slate-400 font-bold">Aksi</p>

            <div class="mt-1 flex items-center gap-3 text-sm font-semibold">
                
                <a href="{{ route('purchases.edit', $purchase->id_purchase) }}"
                class="text-blue-600 hover:text-slate-600 transition">
                    Edit
                </a>

                <span class="text-slate-300">|</span>

                <button type="button"
                    onclick="confirmDelete()"
                    class="text-red-500 hover:text-red-600 transition">
                    Hapus
                </button>
            </div>

            <form id="deleteForm"
                action="{{ route('purchases.destroy', $purchase->id_purchase) }}"
                method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-[32px] border border-slate-100 p-8">

        <h3 class="font-bold text-slate-800 mb-6">Daftar Bahan</h3>

        <table class="w-full text-sm">
            <thead class="text-xs text-slate-400 uppercase">
                <tr>
                    <th class="text-left py-2">Bahan</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Harga</th>
                    <th class="text-center">Subtotal</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @php $total = 0; @endphp

                @foreach($purchase->details as $d)
                @php 
                    $sub = $d->jumlah * $d->harga;
                    $total += $sub;
                @endphp

                <tr>
                    <td class="py-3 font-semibold text-slate-800">
                        {{ $d->material->nama_bahanbaku }}
                    </td>

                    <td class="text-center">{{ $d->jumlah }}</td>

                    <td class="text-center">
                        {{ $d->material->satuan }}
                    </td>

                    <td class="text-center">
                        Rp {{ number_format($d->harga,0,',','.') }}
                    </td>

                    <td class="text-center text-green-600 font-bold">
                        Rp {{ number_format($sub,0,',','.') }}
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>

        <!-- TOTAL -->
        <div class="flex justify-end mt-6">
            <div class="text-right">
                <p class="text-xs text-slate-400">Total</p>
                <p class="text-xl font-black text-green-600">
                    Rp {{ number_format($total,0,',','.') }}
                </p>
            </div>
        </div>

    </div>

</div>

<script>
function confirmDelete() {
    if (confirm('Yakin ingin menghapus pembelian ini?')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection
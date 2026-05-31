@extends('layouts.main')

@section('page_title', 'Laporan Mutasi Stok')

@section('content')
<div class="p-6 md:p-10">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <nav class="flex text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">
                <a href="{{ route('reports.index') }}" class="hover:text-blue-600 transition-colors">Laporan</a>
                <span class="mx-2">/</span>
                <span class="text-slate-800 italic uppercase">Mutasi Stok</span>
            </nav>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">
                Mutasi <span class="text-blue-600">Bahan Baku</span>
            </h1>
        </div>
        
        {{-- Tombol PDF/Excel --}}
        <div class="flex gap-3">
             <button type="submit" 
                    form="formFilterMutation"
                    formaction="{{ route('reports.mutation.pdf') }}" 
                    class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-sm flex items-center group">
                <svg class="w-4 h-4 mr-2 text-rose-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Export PDF
            </button>

            <button type="submit" 
                    form="formFilterMutation"
                    formaction="{{ route('reports.mutation.excel') }}" 
                    class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-sm flex items-center group">
                <svg class="w-4 h-4 mr-2 text-emerald-500 group-hover:translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Excel
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('reports.mutation') }}" method="GET" id="formFilterMutation" class="flex flex-wrap items-end gap-6">
            
            {{-- 1. Pilihan Jenis Bahan (Sudah Ada) --}}
            <div class="flex-1 min-w-[200px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Jenis Bahan</label>
                <select name="material_id" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm outline-none">
                    <option value="">Semua Bahan Baku</option>
                    @foreach($materials as $mat)
                        <option value="{{ $mat->id_bahanbaku }}" {{ $material_id == $mat->id_bahanbaku ? 'selected' : '' }}>{{ $mat->nama_bahanbaku }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 min-w-[150px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Tipe Transaksi</label>
                <select name="transaction_type" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm outline-none">
                    <option value="">Semua Tipe</option>
                    <option value="masuk" {{ $transaction_type == 'masuk' ? 'selected' : '' }}>Masuk</option>
                    <option value="keluar" {{ $transaction_type == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    <option value="penyesuaian" {{ $transaction_type == 'penyesuaian' ? 'selected' : '' }}>Penyesuaian</option>
                </select>
            </div>

            <div class="flex-1 min-w-[150px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Mulai</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm">
            </div>

            <div class="flex-1 min-w-[150px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Sampai</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm">
            </div>

            <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg">
                Tampilkan Riwayat
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-8 py-6">Waktu / ID</th>
                        <th class="px-8 py-6">Nama Bahan Baku</th>
                        <th class="px-8 py-6 text-center">Tipe Transaksi</th>
                        <th class="px-8 py-6 text-center">Jumlah</th>
                        <th class="px-8 py-6">Keterangan / Referensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mutations as $mut)
                    @php
                        $tipe = strtolower($mut->tipe_transaksi);
                        
                        // Konfigurasi warna dan simbol berdasarkan tipe
                        $config = match($tipe) {
                            'masuk' => [
                                'badge'  => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'amount' => 'text-emerald-600',
                                'symbol' => '+'
                            ],
                            'penyesuaian' => [
                                'badge'  => 'bg-amber-50 text-amber-600 border-amber-100',
                                'amount' => 'text-amber-600',
                                'symbol' => '±' // Simbol penyesuaian (bisa nambah/kurang)
                            ],
                            default => [ // Anggap sebagai 'keluar'
                                'badge'  => 'bg-rose-50 text-rose-600 border-rose-100',
                                'amount' => 'text-rose-600',
                                'symbol' => '-'
                            ],
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="px-8 py-5">
                            <span class="block font-bold text-slate-700 text-xs">{{ date('d M Y', strtotime($mut->created_at)) }}</span>
                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest italic">
                                REF #{{ $mut->id_movement }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="font-black text-slate-700 uppercase text-xs">{{ $mut->nama_bahanbaku }}</span>
                        </td>
                        
                        {{-- Kolom Tipe Transaksi --}}
                        <td class="px-8 py-5 text-center">
                            <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $config['badge'] }}">
                                {{ $mut->tipe_transaksi }}
                            </span>
                        </td>

                        {{-- Kolom Jumlah --}}
                        <td class="px-8 py-5 text-center">
                            <span class="text-sm font-black {{ $config['amount'] }}">
                                {{ $config['symbol'] }} {{ number_format($mut->jumlah, 2) }}
                            </span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase ml-1">{{ $mut->satuan }}</span>
                        </td>

                        <td class="px-8 py-5">
                            <p class="text-xs text-slate-500 font-medium italic leading-relaxed">{{ $mut->keterangan ?? '-' }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-slate-300 italic">Data mutasi tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
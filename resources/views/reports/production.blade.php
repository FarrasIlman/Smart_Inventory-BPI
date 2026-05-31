@extends('layouts.main')

@section('page_title', 'Laporan Analisis Produksi')

@section('content')
<div class="p-6 md:p-10">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        {{-- Judul & Breadcrumb --}}
        <div>
            <nav class="flex text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">
                <a href="{{ route('reports.index') }}" class="hover:text-amber-500 transition-colors">Laporan</a>
                <span class="mx-2">/</span>
                <span class="text-slate-800">Produksi</span>
            </nav>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">
                Laporan Produksi
            </h1>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-3">
            
            <button type="submit" form="formFilterData" formaction="{{ route('reports.production.pdf') }}"
                    class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center shadow-sm group">
                <svg class="w-4 h-4 mr-2 text-rose-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Cetak PDF
            </button>

            <button type="submit" 
                    form="formFilterData"
                    formaction="{{ route('reports.production.excel') }}" 
                    class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-semibold text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center shadow-sm group">
                
                <svg class="w-4 h-4 mr-2 text-emerald-500 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>

                EXPORT EXCEL
            </button>
        </div>
    </div>

    {{-- Summary Tiles --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- Total Produksi --}}
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Produksi</p>
            <h3 class="text-2xl font-black text-slate-800">{{ $summary['total_produksi'] }}</h3>
        </div>

        {{-- Antrean --}}
        <div class="bg-violet-500 p-6 rounded-[2rem] text-white shadow-lg shadow-violet-100">
            <p class="text-[9px] font-black uppercase tracking-widest mb-2 opacity-80">Antrean (Siap)</p>
            <h3 class="text-2xl font-black">{{ $summary['siap_proses'] }}</h3>
        </div>

        {{-- Sedang Proses --}}
        <div class="bg-amber-500 p-6 rounded-[2rem] text-white shadow-lg shadow-amber-100">
            <p class="text-[9px] font-black uppercase tracking-widest mb-2 opacity-80">Sedang Proses</p>
            <h3 class="text-2xl font-black">{{ $summary['sedang_jalan'] }}</h3>
        </div>

        {{-- Selesai --}}
        <div class="bg-emerald-500 p-6 rounded-[2rem] text-white shadow-lg shadow-emerald-100">
            <p class="text-[9px] font-black uppercase tracking-widest mb-2 opacity-80">Selesai Produksi</p>
            <h3 class="text-2xl font-black">{{ $summary['tahap_akhir'] }}</h3>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('reports.production') }}" method="GET" id="formFilterData" class="flex flex-wrap items-end gap-6">
            <div class="flex-1 min-w-[180px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl font-bold text-sm">
            </div>

            {{-- Dropdown Multi-Select Tahap --}}
            <div class="flex-1 min-w-[220px] relative" x-data="{ open: false }">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Filter Tahap</label>
                <button type="button" @click="open = !open" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-left flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-700">
                        {{ count($selected_stages) > 0 ? count($selected_stages) . ' Tahap Dipilih' : 'Semua Tahap' }}
                    </span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round"/></svg>
                </button>

                <div x-show="open" @click.away="open = false" class="absolute z-50 mt-2 w-full bg-white border border-slate-100 shadow-xl rounded-2xl p-4 space-y-2" x-cloak x-transition>
                    @php $list_tahap = ['potong', 'branding', 'jahit', 'finishing', 'quality check', 'selesai']; @endphp
                    @foreach($list_tahap as $t)
                    <label class="flex items-center p-2 hover:bg-slate-50 rounded-lg cursor-pointer">
                        <input type="checkbox" name="stages[]" value="{{ $t }}" class="w-4 h-4 text-amber-600 rounded" {{ in_array($t, $selected_stages) ? 'checked' : '' }}>
                        <span class="ml-3 text-xs font-bold text-slate-600 uppercase tracking-wider">{{ $t }}</span>
                    </label>
                    @endforeach
                    <div class="pt-2 mt-2 border-t border-slate-50">
                        <button type="button" @click="open = false; $el.closest('form').submit();" class="w-full text-center text-[9px] font-black text-amber-600 uppercase tracking-widest">Selesai & Terapkan</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="bg-amber-500 text-white px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg shadow-amber-100">
                Terapkan
            </button>
        </form>
    </div>

    {{-- Tabel Produksi --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tgl Mulai</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Order / Pelanggan</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Produk</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Tahap</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($productions as $prod)
                    <tr class="hover:bg-slate-50/50 transition-all text-xs font-bold">
                        {{-- 1. Tanggal --}}
                        <td class="px-8 py-5 text-slate-500 whitespace-nowrap">
                            {{ date('d M Y', strtotime($prod->created_at)) }}
                        </td>

                        {{-- 2. Pelanggan & ID Order --}}
                        <td class="px-8 py-5">
                            <span class="block text-slate-400 text-[10px] mb-1 font-black tracking-widest">#ORD-{{ $prod->id_order }}</span>
                            <span class="block text-slate-700 uppercase tracking-tight font-black">{{ $prod->nama_pelanggan }}</span>
                        </td>

                        {{-- 3. Produk & Qty --}}
                        <td class="px-8 py-5 text-slate-700">
                            <div class="flex flex-col">
                                <span class="text-slate-800 uppercase">{{ $prod->nama_produk }}</span>
                                <span class="text-[10px] text-slate-400 font-medium italic">{{ $prod->jumlah_pesanan }} Pcs Terpesan</span>
                            </div>
                        </td>

                        {{-- 4. Kolom TAHAP PRODUKSI --}}
                        <td class="px-8 py-5 text-center">
                            @php
                                $status_db = strtolower($prod->status_order);
                                $tahap_db = strtolower($prod->tahap_produksi);

                                if($status_db == 'siap produksi') {
                                    $tahap = ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'label' => '⏳ Menunggu Produksi'];
                                } else {
                                    $tahap = match($tahap_db) {
                                        'potong'         => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'label' => '✂️ Potong'],
                                        'branding'       => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-600', 'label' => '🎨 Branding'],
                                        'jahit'          => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => '🪡 Jahit'],
                                        'finishing'      => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-600', 'label' => '✨ Finishing'],
                                        'quality check'  => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'label' => '🔍 QC'],
                                        'selesai'        => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'label' => '✅ Selesai'],
                                        default          => ['bg' => 'bg-slate-50', 'text' => 'text-slate-400', 'label' => $prod->tahap_produksi],
                                    };
                                }
                            @endphp
                            <span class="inline-block px-4 py-2 rounded-xl {{ $tahap['bg'] }} {{ $tahap['text'] }} text-[9px] font-black uppercase italic whitespace-nowrap border border-white shadow-sm">
                                {{ $tahap['label'] }}
                            </span>
                        </td>

                        {{-- 5. Kolom STATUS ORDER --}}
                        <td class="px-8 py-5">
                            @php
                                $status = match($status_db) {
                                    'menunggu bahan' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'dot' => 'bg-orange-500', 'lbl' => 'Menunggu Bahan'],
                                    'siap produksi'  => ['bg' => 'bg-violet-100', 'text' => 'text-violet-700', 'dot' => 'bg-violet-500', 'lbl' => 'Siap Produksi'],
                                    'produksi'       => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'lbl' => 'Produksi'],
                                    'perlu dikirim'  => ['bg' => 'bg-pink-100', 'text' => 'text-pink-700', 'dot' => 'bg-pink-500', 'lbl' => 'Perlu Dikirim'],
                                    'dikirim'        => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500', 'lbl' => 'Dikirim'],
                                    'selesai'        => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'lbl' => 'Selesai'],
                                    default          => ['bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'dot' => 'bg-slate-300', 'lbl' => $prod->status_order],
                                };
                            @endphp
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full {{ $status['bg'] }} {{ $status['text'] }} whitespace-nowrap border border-white shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }}"></span>
                                <span class="text-[9px] font-black uppercase tracking-wider">{{ $status['lbl'] }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-200">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <p class="text-slate-400 font-bold italic text-sm uppercase tracking-widest">Tidak ada antrean produksi pada periode ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
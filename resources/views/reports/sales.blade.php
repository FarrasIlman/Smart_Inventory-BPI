@extends('layouts.main')

@section('page_title', 'Laporan Analisis Penjualan')

@section('content')
<div class="p-6 md:p-10">
    
    {{-- Breadcrumb & Title --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <nav class="flex text-slate-400 text-[10px] font-black uppercase tracking-widest mb-2">
                <a href="{{ route('reports.index') }}" class="hover:text-blue-600 transition-colors">Laporan</a>
                <span class="mx-2">/</span>
                <span class="text-slate-800">Penjualan</span>
            </nav>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight italic uppercase">Laporan Penjualan</h1>
        </div>

        {{-- Action Buttons --}}
        <div class="flex flex-wrap items-center gap-3">
            <button type="submit"
                    form="formFilterSales"
                    formaction="{{ route('reports.sales.pdf') }}"
                class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center shadow-sm group">
                <svg class="w-4 h-4 mr-2 text-rose-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Cetak PDF
            </button>

            <button type="submit"
                    form="formFilterSales"
                    formaction="{{ route('reports.sales.excel') }}"
                    class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm group">
                <svg class="w-4 h-4 mr-2 text-emerald-500 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                EXPORT EXCEL
            </button>
        </div>
    </div>

    {{-- Filter Bar--}}
    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm mb-8">
        <form action="{{ route('reports.sales') }}" method="GET" id="formFilterSales" class="flex flex-wrap items-end gap-6">
            <div class="flex-1 min-w-[180px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-bold text-sm">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 font-bold text-sm">
            </div>

            {{-- Dropdown Multi-Select Status --}}
            <div class="flex-1 min-w-[220px] relative" x-data="{ open: false }">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Status Pesanan</label>
                
                <button type="button" @click="open = !open" 
                    class="w-full bg-slate-50 border border-slate-200 p-3 rounded-xl text-left flex justify-between items-center outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-sm font-bold text-slate-700">
                        @if(count($selected_statuses) > 0)
                            {{ count($selected_statuses) }} Status Dipilih
                        @else
                            Semua Status
                        @endif
                    </span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" 
                    class="absolute z-50 mt-2 w-full bg-white border border-slate-100 shadow-xl rounded-2xl p-4 space-y-2" 
                    x-cloak x-transition>
                    
                    @php
                        $list_status = [
                            'selesai' => ['label' => 'Selesai', 'color' => 'bg-emerald-500'],
                            'dikirim' => ['label' => 'Dikirim', 'color' => 'bg-blue-500'],
                            'produksi' => ['label' => 'Produksi', 'color' => 'bg-amber-500'],
                            'SIAP PRODUKSI'   => ['label' => 'Siap Produksi', 'color' => 'bg-purple-500'],
                            'MENUNGGU BAHAN'  => ['label' => 'Menunggu Bahan', 'color' => 'bg-orange-500'],
                            'PERLU DIKIRIM'   => ['label' => 'Perlu Dikirim', 'color' => 'bg-rose-500'],
                        ];
                    @endphp

                    @foreach($list_status as $key => $data)
                    <label class="flex items-center p-2 hover:bg-slate-50 rounded-lg cursor-pointer transition-all">
                        <input type="checkbox" name="statuses[]" value="{{ $key }}" 
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                            {{ in_array($key, $selected_statuses) ? 'checked' : '' }}>
                        
                        <div class="ml-3 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $data['color'] }}"></span>
                            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">{{ $data['label'] }}</span>
                        </div>
                    </label>
                    @endforeach

                    <div class="pt-2 mt-2 border-t border-slate-50 flex justify-between">
                        <button type="button" 
                            @click="open = false; $el.closest('form').submit();" 
                            class="text-[9px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-800 transition-colors">
                            Selesai
                        </button>
                        <button type="button" onclick="const checks = document.querySelectorAll('input[type=checkbox]'); checks.forEach(c => c.checked = false);" 
                            class="text-[9px] font-black text-rose-400 uppercase tracking-widest">Reset</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                Terapkan
            </button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-blue-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-blue-100">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Total Omzet</p>
            <h2 class="text-3xl font-black tracking-tighter">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</h2>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Total Pesanan</p>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">{{ $summary['total_pesanan'] }} <span class="text-sm font-bold text-slate-300">Orders</span></h2>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Total Produk Terjual</p>
            <h2 class="text-3xl font-black text-slate-800 tracking-tighter">{{ $summary['total_qty'] }} <span class="text-sm font-bold text-slate-300">Pcs</span></h2>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tanggal</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pelanggan</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Produk</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Qty</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Pesanan</th>
                    <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total Harga</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50/50 transition-all text-xs font-bold">
                    <td class="px-8 py-5 text-slate-500">{{ date('d M Y', strtotime($order->created_at)) }}</td>
                    <td class="px-8 py-5 text-slate-700 uppercase">{{ $order->nama_pelanggan }}</td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black rounded-lg uppercase tracking-wide">
                            {{ $order->nama_produk }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-center text-slate-700">{{ $order->jumlah_pesanan }}</td>
                    <td class="px-8 py-5">
                        @php
                            $status_db = strtolower($order->status_order);

                            $status = match($status_db) {
                                'selesai' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'dot' => 'bg-emerald-500', 'label' => 'Selesai'],
                                'dikirim' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'dot' => 'bg-blue-500', 'label' => 'Dikirim'],
                                'sedang produksi', 'produksi' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'dot' => 'bg-amber-500', 'label' => 'Produksi'],
                                'siap produksi' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'dot' => 'bg-violet-500', 'label' => 'Siap Produksi'],
                                'menunggu bahan', 'menunggu' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'dot' => 'bg-orange-500', 'label' => 'Menunggu Bahan'],
                                'perlu dikirim' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'dot' => 'bg-rose-500', 'label' => 'Perlu Dikirim'],
                                default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400', 'label' => $order->status_order],
                            };
                        @endphp
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full {{ $status['bg'] }} {{ $status['text'] }} whitespace-nowrap border border-white shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }}"></span>
                            <span class="text-[9px] font-black uppercase tracking-wider">{{ $status['label'] }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-right font-black text-blue-600">
                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center text-slate-400 italic">
                        Tidak ada data penjualan pada periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
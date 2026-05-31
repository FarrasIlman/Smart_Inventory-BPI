<div 
    x-show="detailModal{{ $order->id_order }}" 
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4" 
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="detailModal{{ $order->id_order }} = false"></div>

    <div 
        class="bg-white w-full max-w-4xl rounded-[32px] shadow-2xl p-8 overflow-hidden relative z-10" 
        x-show="detailModal{{ $order->id_order }}"
        x-transition:enter="transition ease-out duration-300 translate-y-4"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 translate-y-0"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        
        <div class="flex justify-between items-start mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Detail Pesanan #{{ $order->id_order }}</h2>
                <p class="text-slate-400 text-sm italic">{{ $order->nama_pelanggan }} — {{ \Carbon\Carbon::parse($order->tanggal_pesan)->format('d F Y') }}</p>
            </div>
            <button @click="detailModal{{ $order->id_order }} = false" class="p-2 bg-slate-100 text-slate-400 hover:text-red-500 rounded-full transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="space-y-6">
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic font-bold text-blue-600">Informasi Produk</h4>
                    <div class="flex justify-between mb-2">
                        <span class="text-xs text-slate-500">Nama Produk:</span>
                        <span class="text-sm font-bold text-slate-800">{{ $order->product->nama_produk }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-xs text-slate-500">Total Pesanan:</span>
                        <span class="text-sm font-bold text-blue-600">{{ $order->jumlah_pesanan }} Pcs</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1 italic">Rincian Ukuran</h4>
                    <div class="grid grid-cols-4 gap-3">
                        @forelse($order->details as $detail)
                        <div class="p-3 bg-white border border-slate-200 rounded-xl text-center shadow-sm hover:border-blue-300 transition-colors">
                            <p class="text-[9px] font-black text-slate-300 uppercase mb-1">{{ $detail->size }}</p>
                            <p class="text-lg font-bold text-slate-700 leading-none">{{ $detail->quantity }}</p>
                        </div>
                        @empty
                        <div class="col-span-4 p-4 text-center text-xs text-slate-400 italic bg-slate-50 rounded-xl">Data rincian tidak ditemukan</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 px-1 italic">Preview Desain</h4>
                <div class="aspect-square w-full bg-slate-100 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center overflow-hidden">
                    @if($order->gambar_desain)
                        <img src="{{ asset('storage/' . $order->gambar_desain) }}" class="w-full h-full object-contain p-4 transition-transform duration-300 hover:scale-105">
                    @else
                        <div class="text-center p-6 text-slate-300">
                             <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                             <p class="text-xs font-bold uppercase tracking-widest">Tidak Ada Desain</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-10 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <button @click="detailModal{{ $order->id_order }} = false" class="bg-slate-100 text-slate-600 px-8 py-3 rounded-2xl font-bold text-xs hover:bg-slate-200 transition-all uppercase tracking-widest">
                Tutup Detail
            </button>
            <a href="{{ route('orders.edit', $order->id_order) }}" class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold text-xs hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 uppercase tracking-widest flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Edit Pesanan
            </a>
        </div>
    </div>
</div>
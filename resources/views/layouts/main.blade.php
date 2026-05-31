<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - BPI Inventory</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-navy { background-color: #1e263d; }
        .sidebar-item:hover { background-color: rgba(255,255,255,0.08); transition: 0.3s; }
        .active-menu { background-color: #3b82f6; color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
        
        [x-cloak] { 
            display: none !important; 
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-72 bg-navy text-slate-300 flex flex-col shrink-0">
            <div class="p-8 mb-4">
                <img src="/img/BPI Logo White.jpeg" alt="Logo" class="h-10 mb-2">
                <p class="text-[10px] text-blue-400 font-bold tracking-[0.2em] uppercase">Inventory System</p>
            </div>

            <nav class="flex-1 px-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="sidebar-item flex items-center p-3 rounded-xl {{ Request::is('dashboard') ? 'active-menu' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-sm font-semibold">Dashboard</span>
                </a>

                <a href="{{ route('orders.index') }}" class="sidebar-item flex items-center p-3 rounded-xl transition {{ Request::is('orders*') ? 'active-menu' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 11-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <span class="text-sm font-semibold">Pesanan</span>
                </a>

                <a href="{{ route('production.index') }}" 
                class="sidebar-item flex items-center p-3 rounded-xl transition {{ Request::is('production*') ? 'active-menu' : '' }}">
                    
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {{-- Ikon Box/Produksi agar beda dengan Pesanan --}}
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    
                    <span class="text-sm font-semibold">Produksi</span>
                </a>

                <a href="{{ route('items.index') }}" class="sidebar-item flex items-center p-3 rounded-xl transition {{ Request::is('stok-bahan*') ? 'active-menu' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span class="text-sm font-semibold">Stok Bahan Baku</span>
                </a>

                <a href="{{ route('purchases.index') }}" class="sidebar-item flex items-center p-3 rounded-xl transition {{ Request::is('purchases*') || Request::is('pembelian*') ? 'active-menu' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold">Pembelian</span>
                </a>

                <div x-data="{ open: false }">
                    <button @click="open = !open" class="sidebar-item w-full flex items-center justify-between p-3 rounded-xl focus:outline-none transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            <span class="text-sm font-semibold">Data Master</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" class="pl-12 mt-2 space-y-2">
                        <a href="{{ route('products.index') }}" class="block text-xs text-slate-500 hover:text-white transition">Data Produk</a>
                        <a href="{{ route('bom.index') }}" class="block text-xs text-slate-500 hover:text-white transition">Data Bahan Baku</a>
                        <a href="{{ route('suppliers.index') }}" class="block text-xs text-slate-500 hover:text-white transition">Data Supplier</a>
                    </div>
                </div>

                <a href="{{ route('reports.index') }}" class="sidebar-item flex items-center p-3 rounded-xl transition {{ Request::is('reports*') || Request::is('laporan*') ? 'active-menu' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-sm font-semibold">Laporan</span>
                </a>

                @if(auth()->user()->role == 'admin')
                <a href="{{ route('users.index') }}" 
                class="sidebar-item flex items-center p-3 rounded-xl transition {{ Request::is('manajemen-akun*') ? 'active-menu' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span class="text-sm font-semibold">Manajemen Akun</span>
                </a>
                @endif
            </nav>

            <div class="p-6 border-t border-white/5">
                <a href="/login" class="flex items-center text-red-400 hover:text-red-300 transition text-sm font-bold">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 p-6 flex justify-between items-center px-10">
                <h2 class="text-xl font-bold text-navy">@yield('page_title')</h2>
                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Selamat Datang</p>
                        <p class="text-sm font-bold text-slate-800">{{ auth()->user()->nama_user }} -
                            <span class="text-blue-600">{{ strtoupper(auth()->user()->role) }}</span>
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold shadow-lg shadow-blue-100">
                        {{ strtoupper(substr(auth()->user()->nama_user, 0, 1)) }}
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-10">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
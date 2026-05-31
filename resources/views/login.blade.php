<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventory</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script> 
    <style>
        .bg-navy-dark { background-color: #1e263d; }
        .text-navy-dark { color: #1e263d; }
        .focus-ring-navy:focus { --tw-ring-color: #1e263d; border-color: #1e263d; }
    </style>
</head>
<body class="antialiased font-sans text-slate-900">

    <div class="flex h-screen overflow-hidden">
        
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-8 md:p-16 lg:p-24 bg-white">
            
            <div class="flex justify-center">
                <img src="/img/BPI Logo Black.jpeg" alt="BUMIPUTERA Logo" class="h-14">
            </div>

            <div class="max-w-md w-full mx-auto lg:mx-0">
                <h1 class="text-2xl md:text-3xl font-bold text-navy-dark mb-2 text-center lg:text-left">
                    Masuk ke Sistem Inventory
                </h1>
                <p class="text-gray-500 mb-8 text-center lg:text-left text-sm">Silakan masukkan akun Anda untuk melanjutkan.</p>

                @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r shadow-sm flex items-center">
                    <svg class="h-5 w-5 text-red-500 mr-3" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm text-red-700 font-medium">{{ $errors->first() }}</p>
                </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Masukkan username anda" 
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus-ring-navy outline-none transition" required>
                    </div>

                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi anda" 
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus-ring-navy outline-none transition" required>
                            
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-navy-dark transition">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path id="eyePath" stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-navy-dark border-gray-300 rounded focus:ring-navy-dark cursor-pointer">
                        <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                            Ingat Saya
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-navy-dark text-white font-semibold py-3 rounded-lg shadow-lg hover:bg-slate-800 transition duration-300">
                        Masuk
                    </button>
                </form>
            </div>

            <div class="text-center lg:text-left text-gray-400 text-xs">
                © 2026 Bumiputera Persada
            </div>

        </div>

        <div class="hidden lg:flex lg:w-1/2 bg-navy-dark flex-col justify-center items-center text-white p-12 relative">
            
            <div class="text-center mb-12">
                <img src="/img/BPI_Logo.png" alt="Logo White" class="h-20 mx-auto mb-6">
                <h2 class="text-3xl font-bold tracking-tight">Sistem Inventory</h2>
                <p class="text-blue-300 mt-2 font-medium italic">Solusi Cerdas Manajemen Industri</p>
            </div>

            <div class="space-y-8 max-w-sm">
                
                <div class="flex items-start space-x-4">
                    <div class="bg-blue-500 bg-opacity-20 p-3 rounded-xl">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Laporan Real-time</h3>
                        <p class="text-sm text-gray-400">Pantau semua laporan stok dan transaksi secara instan.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="bg-blue-500 bg-opacity-20 p-3 rounded-xl">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Manajemen Bahan Baku</h3>
                        <p class="text-sm text-gray-400">Kelola data bahan baku dengan kontrol penuh.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="bg-blue-500 bg-opacity-20 p-3 rounded-xl">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="16" y1="14" x2="16" y2="18"></line><path d="M16 10h.01"></path><path d="M12 10h.01"></path><path d="M8 10h.01"></path><path d="M12 14h.01"></path><path d="M8 14h.01"></path><path d="M12 18h.01"></path><path d="M8 18h.01"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Perhitungan Otomatis</h3>
                        <p class="text-sm text-gray-400">Akurasi perhitungan bahan tanpa perlu manual.</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4">
                    <div class="bg-blue-500 bg-opacity-20 p-3 rounded-xl">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Informasi Supplier</h3>
                        <p class="text-sm text-gray-400">Akses cepat ke seluruh data kontak dan riwayat supplier.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyePath = document.getElementById('eyePath');
            
            // Icon Mata Terbuka
            const eyeOpen = "M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z";
            // Icon Mata Tertutup
            const eyeClosed = "M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88";

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyePath.setAttribute('d', eyeOpen);
            } else {
                passwordInput.type = 'password';
                eyePath.setAttribute('d', eyeClosed);
            }
        }
    </script>
</body>
</html>
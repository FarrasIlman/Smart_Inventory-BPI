@extends('layouts.main')

@section('page_title', 'Manajemen Akun')

@section('content')
{{-- x-data utama untuk modal tambah --}}
<div x-data="{ openModal: false }">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h3 class="text-slate-800 font-bold text-xl tracking-tight">Pengaturan Akun</h3>
            <p class="text-slate-400 text-xs mt-1">Total ada <span class="font-bold text-blue-600">{{ $users->count() }}</span> akun terdaftar dalam sistem</p>
        </div>
        <button @click="openModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-blue-200 transition-all flex items-center group">
            <svg class="w-4 h-4 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Akun
        </button>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-bold tracking-widest border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5">Nama User</th>
                    <th class="px-8 py-5">Username</th>
                    <th class="px-8 py-5 text-center">Akses / Role</th>
                    <th class="px-8 py-5 text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($users as $user)
                {{-- x-data per baris khusus untuk modal edit --}}
                <tr class="hover:bg-slate-50/50 transition-colors" x-data="{ editModal: false }">
                    <td class="px-8 py-5">
                        <div class="flex items-center">
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs mr-3 shadow-sm">
                                {{ strtoupper(substr($user->nama_user, 0, 1)) }}
                            </div>
                            <span class="font-bold text-slate-700">{{ $user->nama_user }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-slate-500 font-mono text-xs italic">{{ $user->username }}</td>
                    <td class="px-8 py-5 text-center">
                        <span class="px-3 py-1.5 bg-indigo-50 text-indigo-600 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-indigo-100">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right space-x-2">
                        <button @click="editModal = true" class="text-blue-600 hover:text-blue-800 font-bold py-2 px-3 rounded-lg hover:bg-blue-50 transition-all">
                            Edit
                        </button>

                        <form action="{{ route('users.destroy', $user->id_user) }}" method="POST" class="inline">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus akun {{ $user->nama_user }}?')" class="text-red-400 hover:text-red-600 font-bold py-2 px-3 rounded-lg hover:bg-red-50 transition-all">
                                Hapus
                            </button>
                        </form>

                        <div x-show="editModal" 
                             class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" 
                             style="display: none;" x-cloak x-transition>
                            <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-8 text-left" @click.away="editModal = false">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-slate-800">Edit Data Akun</h3>
                                    <button @click="editModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
                                </div>
                                
                                <form action="{{ route('users.update', $user->id_user) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Nama Lengkap</label>
                                        <input type="text" name="nama_user" value="{{ $user->nama_user }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Username</label>
                                        <input type="text" name="username" value="{{ $user->username }}" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Password Baru (Opsional)</label>
                                        <input type="password" name="password" placeholder="Kosongkan jika tidak diganti" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Role</label>
                                        <select name="role" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="produksi" {{ $user->role == 'produksi' ? 'selected' : '' }}>Produksi</option>
                                            <option value="manajerial" {{ $user->role == 'manajerial' ? 'selected' : '' }}>Manajerial</option>
                                            <option value="gudang" {{ $user->role == 'gudang' ?
                                            'selected' : '' }}>Gudang</option>
                                        </select>
                                    </div>
                                    <div class="pt-6 flex justify-end space-x-3">
                                        <button type="button" @click="editModal = false" class="px-6 py-2 text-slate-400 font-bold">Batal</button>
                                        <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="openModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4" 
         style="display: none;" x-cloak x-transition>
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl p-8 transform transition-all" @click.away="openModal = false">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">Buat Akun Baru</h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
            </div>
            
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_user" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="Masukkan nama" required>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Username</label>
                    <input type="text" name="username" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="Untuk login" required>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Password</label>
                    <input type="password" name="password" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Role / Hak Akses</label>
                    <select name="role" class="w-full bg-slate-50 border border-slate-200 p-3.5 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="manajerial">Manajerial</option>
                        <option value="admin">Admin</option>
                        <option value="produksi">Produksi</option>
                        <option value="gudang">Gudang</option>
                    </select>
                </div>
                <div class="pt-6 flex justify-end space-x-3">
                    <button type="button" @click="openModal = false" class="px-6 py-2 text-slate-400 font-bold">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
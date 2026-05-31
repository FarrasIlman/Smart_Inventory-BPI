<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // 1. function show page
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return view('users.manageusers', compact('users'));
    }

    // 2. function add account
    public function store(Request $request)
    {
        $request->validate([
            'nama_user' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users',
            'password'  => 'required|string|min:5',
            'role'      => 'required|in:manajerial,admin,produksi,gudang'
        ]);

        User::create([
            'nama_user' => $request->nama_user,
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
        ]);

        return redirect()->back()->with('success', 'Akun ' . $request->nama_user . ' berhasil dibuat!');
    }

    // 3. function edit account
    public function update(Request $request, $id){
        $user = User::where('id_user', $id)->firstOrFail();

        $request->validate([
            'nama_user' => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $id . ',id_user',
            'role'      => 'required|in:manajerial,admin,produksi,gudang',
            'password'  => 'nullable|min:5'
        ]);

        $user->nama_user = $request->nama_user;
        $user->username = $request->username;
        $user->role = $request->role;

        if ($request->password) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Akun berhasil diperbarui!');
    }
    
    public function destroy($id)
    {
        $user = User::where('id_user', $id)->firstOrFail();
        $user->delete();
        
        return redirect()->back()->with('success', 'Akun berhasil dihapus!');
    }
}
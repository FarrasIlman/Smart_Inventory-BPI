<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        //  Filter Search
        if ($request->search) {
            $query->where('nama_supplier', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        // 3. Filter Status
        if ($request->status) {
            $query->where('status_supplier', $request->status);
        }

        $suppliers = $query->latest()->get();
        return view('suppliers.supplier', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'email' => 'nullable|email',
            'status_supplier' => 'required|in:aktif,tidak aktif',
        ]);

        Supplier::create($request->all());
        return redirect()->back()->with('success', 'Supplier berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());
        return redirect()->back()->with('success', 'Data supplier diperbarui!');
    }

    public function destroy($id)
    {
        Supplier::destroy($id);
        return redirect()->back()->with('success', 'Supplier berhasil dihapus!');
    }
}
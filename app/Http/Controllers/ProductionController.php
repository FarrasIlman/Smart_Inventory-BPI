<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\RawMaterial;
use App\Models\Production;
use App\Models\ProductionMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function cuttingForm($id)
    {
        $userRole = strtolower(auth()->user()->role ?? '');
        if (!in_array($userRole, ['admin', 'produksi'])) {
            abort(403, 'Akses ditolak.');
        }

        $order = Order::with(['product.boms.rawMaterial', 'details', 'production'])->findOrFail($id);

        if (strtolower($order->status_order) == 'menunggu bahan') {
            abort(403, 'Form Potong belum rilis.');
        }

        return view('production.cutting_form', compact('order'));
    }

    public function updateCuttingForm(Request $request, $id)
    {
        $userRole = strtolower(auth()->user()->role ?? '');
        if (!in_array($userRole, ['admin', 'produksi'])) {
            abort(403, 'Akses ditolak.');
        }

        $production = Production::updateOrCreate(
            ['id_order' => $id],
            [
                'warna_artikel'    => $request->input('warna_artikel'),
                'model_potongan'   => $request->input('model_potongan'),
                'petugas' => $request->input('petugas'),
                'deadline_potong'  => $request->input('deadline_potong'),
                'catatan_potong'   => $request->input('catatan_potong'),
            ]
        );

        return redirect()->back()->with('success', 'Form Potong Berhasil Diperbarui!');
    }
}
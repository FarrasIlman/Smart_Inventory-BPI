<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'id_order';

    protected $fillable = ['nama_pelanggan', 'id_product', 'jumlah_pesanan', 'deadline', 'status_order', 'gambar_desain','tanggal_pesan','tahap_produksi','harga_satuan','total_harga','alamat','no_telepon'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'id_order');
    }
    public function production()
    {
        return $this->hasOne(Production::class, 'id_order');
    }
    public function shipping()
    {
        return $this->hasOne(Shipping::class, 'id_order', 'id_order');
    }
}
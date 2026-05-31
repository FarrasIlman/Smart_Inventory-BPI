<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $table = 'shippings';
    protected $primaryKey = 'id_shipping';

    protected $fillable = [
        'id_order',
        'kurir',
        'nomor_resi',
        'tanggal_pickup',
        'tanggal_delivery',
        'status_pengiriman',
        'biaya_ongkir'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $primaryKey = 'id_purchase';

    protected $fillable = [
        'id_supplier',
        'tanggal_pembelian',
        'status_pembelian',
        'total'
    ];

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class,'id_supplier');
    }

    public function details()
    {
        return $this->hasMany(\App\Models\PurchaseDetail::class,'id_purchase');
    }
}
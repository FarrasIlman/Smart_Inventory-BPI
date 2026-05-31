<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_purchase',
        'id_bahanbaku',
        'jumlah',
        'harga',
        'subtotal'
    ];

    public function material()
    {
        return $this->belongsTo(\App\Models\RawMaterial::class,'id_bahanbaku');
    }
    public function rawMaterial() 
    {
        return $this->belongsTo(RawMaterial::class, 'id_bahanbaku');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bom extends Model {
    protected $table = 'bom';
    protected $primaryKey = 'id_bom';
    public $timestamps = false; 
    protected $fillable = ['id_product', 'id_bahanbaku', 'jumlah_kebutuhan', 'persentase_waste'];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'id_bahanbaku', 'id_bahanbaku');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
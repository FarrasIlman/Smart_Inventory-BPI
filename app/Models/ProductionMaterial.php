<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionMaterial extends Model
{
    protected $table = 'production_materials';

    protected $primaryKey = 'id_usage';

    public $timestamps = false;

    protected $fillable = [
        'id_production',
        'id_bahanbaku',
        'jumlah_estimasi',
        'jumlah_realisasi',
        'harga',
        'subtotal'
    ];

    public function material()
    {
        return $this->belongsTo(RawMaterial::class, 'id_bahanbaku');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class, 'id_bahanbaku');
    }

    public function production()
    {
        return $this->belongsTo(Production::class, 'id_production');
    }
}
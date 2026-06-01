<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $table = 'productions';
    protected $primaryKey = 'id_production';

    protected $fillable = [
        'id_order',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_produksi',
        'warna_artikel',
        'model_potongan',
        'petugas',
        'deadline_potong',
        'catatan_potong'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }

    public function materials()
    {
        return $this->hasMany(ProductionMaterial::class, 'id_production');
    }
}
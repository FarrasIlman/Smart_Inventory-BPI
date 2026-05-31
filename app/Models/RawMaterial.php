<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $table = 'raw_materials';
    protected $primaryKey = 'id_bahanbaku';

    protected $fillable = [
        'nama_bahanbaku', 
        'satuan', 
        'stok',
        'stok_terkunci',
        'harga',
        'stok_minimum', 
        'gambar_bahan'
    ];
}
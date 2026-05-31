<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id_product';
    protected $fillable = [
        'id_categories', 
        'nama_produk', 
        'kategori_produk',
        'estimasi_harga', 
        'deskripsi', 
        'gambar_produk'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_categories');
    }

    public function boms()
    {
        return $this->hasMany(Bom::class, 'id_product', 'id_product');
    }
    
}
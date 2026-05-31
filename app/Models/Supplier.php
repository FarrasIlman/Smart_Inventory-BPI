<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    protected $primaryKey = 'id_supplier';

    protected $fillable = [
        'kode_supplier', 'nama_supplier', 'nama_pic', 
        'no_telepon', 'email', 'alamat', 'kota','keterangan', 
        'lead_time', 'minimum_order', 'status_supplier'
    ];
}
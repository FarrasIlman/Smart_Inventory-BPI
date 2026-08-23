<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceDetail extends Model
{
    use HasFactory;

    protected $table = 'service_detail';

    protected $fillable = [
        'service_id', 'name', 'description', 'image_url', 'image_public_id', 'sort_order'
    ];

    public function service()
    {
        return $this->belongsTo(Services::class, 'service_id');
    }
}

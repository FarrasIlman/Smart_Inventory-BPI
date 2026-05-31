<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id_movement';
    public $incrementing = true;

    protected $guarded = [];

    public $timestamps = true; 
}
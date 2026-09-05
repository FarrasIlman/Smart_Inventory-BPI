<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table = 'services';

    protected $casts = [
        'bg_config' => 'array'
    ];

    protected $fillable = [
        'log_id', 
        'title', 
        'description', 
        'bg_config'
    ];

    public function logs() 
    {
        return $this->belongsTo(CMSLogs::class, 'log_id');
    } 

    public function details()
    {
        return $this->hasMany(ServiceDetail::class, 'service_id');
    }
}

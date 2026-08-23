<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Services extends Model
{
    protected $table = 'services';

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

    public function ServiceList()
    {
        return $this->hasMany(ServiceDetail::class, 'service_id');
    }
}

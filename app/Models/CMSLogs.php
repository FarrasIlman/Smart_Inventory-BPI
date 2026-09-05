<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CMSLogs extends Model
{
    use HasFactory;

    protected $table = 'cms_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'created_by', 
        'status', 
        'notes',
        'created_at', 
        'updated_at'
    ];


    public function hero() 
    {
        return $this->hasMany(HeroContent::class, 'log_id');
    }

    public function aboutUs()
    {
        return $this->hasMany(AboutUs::class, 'log_id');
    }

    public function services()
    {
        return $this->hasMany(Services::class, 'log_id');
    }
}

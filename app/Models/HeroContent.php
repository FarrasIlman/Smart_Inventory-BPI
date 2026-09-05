<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroContent extends Model
{
    use HasFactory;

    protected $table = 'hero_content';

    protected $casts = [
        'is_masked' => 'boolean', 
        'opacity' => 'integer'
    ];

    protected $fillable = [
        'title', 
        'subtitle', 
        'position', 
        'image_id', 
        'image_mobile_id',
        'is_masked', 
        'opacity', 
        'log_id'
    ];

    public function logs() 
    {
        return $this->belongsTo(CMSLogs::class, 'log_id');
    }

    public function images()
    {
        return $this->belongsTo(Images::class, 'image_id');
    }

    public function imageMobile()
    {
        return $this->belongsTo(Images::class, 'image_mobile_id');
    }
}

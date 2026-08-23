<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeroContent extends Model
{
    use HasFactory;

    protected $table = 'hero_content';

    protected $fillable = [
        'title', 
        'subtitle', 
        'position', 
        'image_mobile', 
        'mobile_public_id', 
        'image_url', 
        'image_public_id', 
        'is_masked', 
        'opacity', 
        'sort_order', 
        'log_id'
    ];

    public function logs() 
    {
        return $this->belongsTo(CMSLogs::class, 'log_id');
    }
}

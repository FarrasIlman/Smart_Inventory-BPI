<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $table = 'images';

    protected $fillable = [
        'image_url', 
        'image_public_id', 
        'image_size', 
        'file_name'
    ];

    public function heroDesktop()
    {
        return $this->hasMany(HeroContent::class, 'image_id');
    }

    public function heroMobile()
    {
        return $this->hasMany(HeroContent::class, 'image_mobile_id');
    }
}

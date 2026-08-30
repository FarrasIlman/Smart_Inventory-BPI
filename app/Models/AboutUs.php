<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $table = 'aboutus_content';

    protected $casts = [
        'metrics' => 'array',
    ];

    protected $fillable = [
        'log_id', 'title', 'description', 'bg_config', 'metrics', 'image_id'
    ];

    public function logs()
    {
        return $this->belongsTo(CMSLogs::class, 'log_id');
    }
}

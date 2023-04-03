<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'video_id','round_id'
    ];
    public function video()
    {
        return $this->hasMany(Video::class);
    }
}

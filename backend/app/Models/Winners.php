<?php

namespace App\Models;

use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;

class Winners extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'detail',
        'video_id',
    ];

    protected $with = ['user','video'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }
    
}

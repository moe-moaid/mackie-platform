<?php

namespace App\Models;

use App\Models\RoundVideos;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;

class RoundDetails extends Model
{
    protected $table = "round_details";
    protected $fillable = [
        'name',
        'min_videos','max_videos','details','is_active','extra_details'
    ];


    public function videos()
    {
        return $this->belongsToMany(Video::class, 'round_videos', 'round_id', 'video_id'); //pivot Table
    }

    public function users()
    {
        return $this->hasMany(User::class, 'round', 'id');
    }
}

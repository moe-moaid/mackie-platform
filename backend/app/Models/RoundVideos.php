<?php

namespace App\Models;

use App\Models\RoundDetails;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;

class RoundVideos extends Model
{
    protected $table = "round_videos";
    protected $fillable = [
        'video_id','round_id'
    ];

    protected $with = ['objRound'];

    public function objVideo()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function objRound()
    {
        return $this->belongsTo(RoundDetails::class, 'round_id');
    }

   
}

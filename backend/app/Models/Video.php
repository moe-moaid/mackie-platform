<?php

namespace App\Models;

use App\Models\RoundDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $fillable = [
        'song_name',
        'count',
        'video',
        'user_id',
        'genre_id',
        'share_link',
    ];

    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
	 public function transaction()
    {
        return $this->hasMany(Vote::class);
    }
    public function objRound()
    {
        return $this->belongsToMany(RoundDetails::class, 'round_videos', 'video_id', 'round_id'); //pivot Table
    }
}

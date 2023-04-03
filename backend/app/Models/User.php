<?php

namespace App\Models;

use App\Models\RoundDetails;
use App\Models\Video;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'round','can_upload','apeal_count',
        'password',
        'role',
        'address',
        'age',
        'bio',
        'image',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function objRound()
    {
        return $this->belongsTo(RoundDetails::class, 'round');
    }

    public function eligibleForUploadingVideo()
    {
        if($this->can_upload == 1)
        {
            return true;
        }
        return false;

        $objRoundDetails = RoundDetails::with('videos')->where('is_active',1)->first();
        if($objRoundDetails != null)
        {
            $userCount = 0;
            foreach ($objRoundDetails->videos as $key => $objRoundVideos) 
            {
                if($objRoundVideos->user->id == $this->id)
                {
                    $userCount++;
                }
            }
            dd($userCount);
        }

        return false;
    }

    public function ApplyForEligiblityRoundVideo()
    {
        $objRoundDetails = RoundDetails::with('videos')->where('is_active',1)->first();
        if($objRoundDetails != null)
        {
            $userCount = 0;
            foreach ($objRoundDetails->videos as $key => $objRoundVideos) 
            {
                if($objRoundVideos->user->id == $this->id)
                {
                    $userCount++;
                }
            }
            if($userCount > 1)
            {
                return true;
            }else
            {
                return false;
            } 
        }
        return false;
        
    }

    public function getImageAttribute($val)
    {
        return ($val == null) ? 'null.png' : str_replace("users","",$val);
    }

    public function objVideos()
    {
        return $this->hasMany(Video::class, 'user_id');
    }
}

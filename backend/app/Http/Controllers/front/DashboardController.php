<?php

namespace App\Http\Controllers\front;

use App\Helpers\GeneralHelpers;
use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\RoundDetails;
use App\Models\RoundVideos;
use App\Models\Setting;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JildertMiedema\LaravelPlupload\Facades\Plupload;
use PragmaRX\Countries\Package\Countries;

class DashboardController extends Controller
{
    //
    public function viewProfile($id)
    {
        try
        {
            $objUser = User::with('objVideos')->findOrFail($id);
            $data = [
                'objUser' => $objUser
            ];
            return view('front.profile', $data);

        }catch(\Exception $e)
        {
            return abort(500);
        }
    }
    public function index()
    {
        // phpinfo();die();
        // dd(tmpfile());
        $data['title'] = 'Dashboard';
        $data['setting'] = Setting::where('id', '=', 1)->first();
        $user_id = Auth::guard('user')->user()->id;
		$data['genres']=Genre::where('status','=',1)->get();
        $data['objVideos'] = Video::where('user_id', '=', $user_id)->get(); 

        $objRound =  RoundDetails::where('is_active',1)->firstOrFail();
        $data['objRound'] = $objRound;

        $objRoundVideos = RoundVideos::with('objVideo','objVideo.votes')->where('round_id', $objRound->id)->get();

        $topVideos = [];
        foreach ($objRoundVideos as $key => $roundVideo) 
        {
            if(count($roundVideo->objVideo->votes) < 1)
            {
                continue;
            }
            $topVideos[] = [
                'video_id'  => $roundVideo->video_id,
                'name'      => $roundVideo->objVideo->song_name,
                'watch'     => $roundVideo->objVideo->count,
                'votes'     => count($roundVideo->objVideo->votes),
            ];
        }
        $topVideos              = collect($topVideos)->sortByDesc('votes')->take(($objRound->max_videos + (($objRound->max_videos *10) /100)));
        $selectedVideosForRound = $topVideos->sortByDesc('watch')->take($objRound->max_videos);

        $data['selectedVideosForRound'] = $selectedVideosForRound;
        $GoodVideoData = [];
        foreach ($data['objVideos'] as $key => $objVideo) {
            $isVideoGood    = ($selectedVideosForRound->where('video_id',$objVideo->id)->isEmpty()) ? false : true;
            $GoodVideoData[] = [
                'video_id'      => $objVideo->id,
                'isVideoGood'   => $isVideoGood,
            ];

        }
        $data['GoodVideoData'] = collect($GoodVideoData);

        $data['countries'] = GeneralHelpers::countryList();
        return view('front.dashboard', $data);
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\RoundDetails;
use App\Models\RoundVideos;
use App\Models\User;
use App\Models\Video;
use App\Models\Winners;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class RoundsController extends Controller
{
    public function index()
    {
        try
        {
            $data['activePage'] = 'rounds';
            $data['title'] = 'Rounds Details';
            $data['objRoundDetails'] = RoundDetails::with('videos')->get();
            return view('admin.rounds.index', $data);
        }catch(\Exception $e)
        {
            report($e);
            return back()->withErrors($e)->withInput();
        }
    }
    

    public function show($id)
    {
        try
        {
             
            $objRoundDetails = RoundDetails::with('videos','videos.votes')->findOrFail($id);
            $data['activePage'] = 'rounds';
            $data['title'] = 'Rounds Videos';
            $data['objRoundDetails'] = $objRoundDetails;
            return view('admin.rounds.show', $data);
        }catch(\Exception $e)
        {
            dd($e);
            report($e);
           return back()->withErrors($e) ->withInput();
        }
    }

    public function startThisRound($roundId)
    {
        try
        {
            DB::beginTransaction();


            // RoundDetails::where('is_active',1)->update(['is_active' => 0]);
            $objActiveRoundDetails = RoundDetails::where('is_active',1)->first();
            $objRound = RoundDetails::findOrFail($roundId);
            $selectedVideosForRound = [];
            if($objRound->name == "Round 2")
            {
                $objActiveRoundDetails->is_active = 0;
                $objActiveRoundDetails->save();
                $objRoundVideos = RoundVideos::with('objVideo','objVideo.votes')->where('round_id', RoundDetails::where('name','Round 1')->firstOrFail()->id)->get();

                //FETCH VIDEO LOGIC 
                $topVideos = [];
                foreach ($objRoundVideos as $key => $roundVideo) 
                {
                    if(count($roundVideo->objVideo->votes) < 1)
                    {
                        continue;
                    }
                    $topVideos[] = [
                        'video_id'  => $roundVideo->video_id,
                        'user_id'   => $roundVideo->objVideo->user->id,
                        'name'      => $roundVideo->objVideo->song_name,
                        'watch'     => $roundVideo->objVideo->count,
                        'votes'     => count($roundVideo->objVideo->votes),
                    ];
                }
                $topVideos = collect($topVideos)->sortByDesc('votes')->take(($objRound->max_videos + (($objRound->max_videos *10) /100)));
                $selectedVideosForRound = $topVideos->sortByDesc('watch')->take($objRound->max_videos);
                //FETCH VIDEO LOGIC 


                
            }else if($objRound->name == "Round 3")
            {
                $objActiveRoundDetails->is_active = 0;
                $objActiveRoundDetails->save();
                $objRoundVideos = RoundVideos::with('objVideo','objVideo.votes')->where('round_id', RoundDetails::where('name','Round 2')->firstOrFail()->id)->get();

                //FETCH VIDEO LOGIC 
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
                        'user_id'   => $roundVideo->objVideo->user->id,
                        'watch'     => $roundVideo->objVideo->count,
                        'votes'     => count($roundVideo->objVideo->votes),
                    ];
                }
                $topVideos = collect($topVideos)->sortByDesc('votes')->take(($objRound->max_videos + (($objRound->max_videos *10) /100)));
                $selectedVideosForRound = $topVideos->sortByDesc('watch')->take($objRound->max_videos);
                //FETCH VIDEO LOGIC 

            }else if($objRound->name == "Round 4")
            {
                $objActiveRoundDetails->is_active = 0;
                $objActiveRoundDetails->save();
                $objRoundVideos = RoundVideos::with('objVideo','objVideo.votes')->where('round_id', RoundDetails::where('name','Round 3')->firstOrFail()->id)->get();

                //FETCH VIDEO LOGIC 
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
                        'user_id'   => $roundVideo->objVideo->user->id,
                        'watch'     => $roundVideo->objVideo->count,
                        'votes'     => count($roundVideo->objVideo->votes),
                    ];
                }
                $topVideos = collect($topVideos)->sortByDesc('votes')->take(($objRound->max_videos + (($objRound->max_videos *10) /100)));
                $selectedVideosForRound = $topVideos->sortByDesc('watch')->take($objRound->max_videos);
                //FETCH VIDEO LOGIC 

            }else if($objRound->name == "Round 5")
            {
                return redirect()->route('rounds.selectRoundVideos',['roundId' => $objRound->id]);

            }else if($objRound->name == "Winners")
            {
                return redirect()->route('rounds.selectRoundVideos',['roundId' => $objRound->id]);
            }else
            {
                $objRoundVideos = RoundVideos::with('objVideo','objVideo.votes')->where('round_id', RoundDetails::where('name','Round 1')->firstOrFail()->id)->get();

            }
            if($selectedVideosForRound->isEmpty())
            {
                Session::flash('error', 'Sorry theres no Video Selected For this round. please wait for the votes');
                return redirect()->back();
            }

            foreach($selectedVideosForRound as $roundVideo)
            {
                $data = [
                    'video_id' => $roundVideo['video_id'],
                    'round_id'  =>  $objRound->id
                ];
                // $tempObj = RoundVideos::create($data);
                // $tempObj->save();

                $objUser = User::where('id',$roundVideo['user_id'])->first();
                if($objUser != null)
                {
                    $objUser->round         = $objRound->id;
                    $objUser->can_upload    = 1;
                    $objUser->save();
                }
            }
            $objRound->is_active = 1;
            $objRound->save();
            DB::commit();

            return redirect()->route('rounds.index');
        }catch(\Exception $e)
        {
            DB::rollBack();
            dd($e);
            report($e);
            // dd($e);
           return redirect()->route('rounds.index');
        }
    }


    public function selectRoundVideos($roundId)
    {
        try
        {
             
            $objOrignalRoundDetails = RoundDetails::with('videos','videos.votes')->findOrFail($roundId);
            $objRoundVideos = [];
            if($objOrignalRoundDetails->name == "Round 5")
            {
                $objRoundDetails = RoundDetails::with('videos','videos.votes')->where('id', RoundDetails::where('name','Round 4')->first()->id)->firstOrFail();

            }else if($objOrignalRoundDetails->name == "Winners")
            {
                $objRoundDetails = RoundDetails::with('videos','videos.votes')->where('id', RoundDetails::where('name','Round 5')->first()->id)->firstOrFail();
            }
            $data['activePage']             = 'rounds';
            $data['title']                  = 'Rounds Videos';
            $data['objRoundDetails']        = $objRoundDetails;
            $data['objOrignalRoundDetails'] = $objOrignalRoundDetails;
            // dd($data);
            return view('admin.rounds.selectRound', $data);
        }catch(\Exception $e)
        {
            dd($e);
            report($e);
           return back()->withErrors($e) ->withInput();
        }
    }

    public function edit($getId)
    {
         try
        {
            $objData = RoundDetails::findOrFail($getId);
            $passData = [
                'activePage'    => 'rounds',
                "title" => "Edit Round" ,
                "objData" => $objData,
            ];
            return view('admin.rounds.edit')->with($passData);
        }catch(\Exception $e)
        { 
            report($e);
            dd($e);
            return back()->withErrors($e)
                        ->withInput();
        }
    }
 

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { 
       
    }

    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function update($id ,Request $request)
    {
         $data = [
            'name'    => 'required',
            'details'  => 'required',
            'min_videos' => 'required',
            'max_videos'  => 'required',
        ];
        $reqFields = ['name','details' ,'min_videos','max_videos'];
        $validator = \Validator::make($request->all(), $data);
        try
        {
            if($validator->fails())
            {
                throw new ValidationException($validator);             
            }
            $inputData = $request->all($reqFields);
            DB::beginTransaction();
            
            $objRoundDetails = RoundDetails::findOrFail($id);
            $objRoundDetails->name          = $request->name;
            $objRoundDetails->details       = $request->details;
            $objRoundDetails->min_videos    = $request->min_videos;
            $objRoundDetails->max_videos    = $request->max_videos;

            $objRoundDetails->save();
            DB::commit();
            return redirect()->route('rounds.index');

        }catch(ValidationException $e)
        {
            DB::rollBack();
            dd($e->errors());
           return back();
        }catch(\Exception $e)
        {
            DB::rollBack();
            dd($e);
            report($e);
            return back()->withErrors($e)
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function resetComp()
    { 
        try
        {
            DB::beginTransaction();

            RoundDetails::where('is_active',1)->update(['is_active' => 0]);
            $objRound1 = RoundDetails::where('name','Round 1')->firstOrFail();

            RoundVideos::truncate();

            $objVideos = Video::get();
            foreach ($objVideos as $key => $obj) 
            {
                $data = [
                    'video_id' => $obj->id,
                    'round_id'  =>  $objRound1->id
                ];
                $tempObj = RoundVideos::create($data);
                $tempObj->save();

                $obj->status = 0;
                $obj->save();

                $o = User::query()->update(['can_upload' => 1,'round' => 1]);
            }

            $objRound1->is_active = 1;
            $objRound1->save();



            return redirect()->route('rounds.index');

            DB::commit();

        }catch(\Exception $e)
        {
            DB::rollBack();
            dd($e);
            report($e);
            return back()->withErrors($e)
                        ->withInput();
        }
    }

    public function startThisRoundCustom($roundId,Request $request)
    {
        try
        {
            DB::beginTransaction();

            $selectedVideosId       = $request->videos;
            $objActiveRoundDetails  = RoundDetails::where('is_active',1)->first();



            $objRound = RoundDetails::findOrFail($roundId);
            $objRoundVideos = Video::whereIn('id',$selectedVideosId)->get();

            //FETCH VIDEO LOGIC 
            $topVideos = [];
            foreach ($objRoundVideos as $key => $roundVideo) 
            {
                if(count($roundVideo->votes) < 1 && $objRound->name != "Winners")
                {
                    continue;
                }
                $topVideos[] = [
                    'video_id'  => $roundVideo->id,
                    'user_id'      => $roundVideo->user->id,
                    'name'      => $roundVideo->song_name,
                    'watch'     => $roundVideo->count,
                    'votes'     => count($roundVideo->votes),
                ];
            }

            if($objRound->name == "Winners")
            {
                $selectedVideosForRound = $topVideos;
               
            }else
            {
                $topVideos = collect($topVideos)->sortByDesc('votes')->take(($objRound->max_videos + (($objRound->max_videos *10) /100)));
                $selectedVideosForRound = $topVideos->sortByDesc('watch')->take($objRound->max_videos);
            }
            
            
            RoundVideos::where('round_id',$objRound->id)->delete();

            foreach($selectedVideosForRound as $roundVideo)
            {
                $data = [
                    'video_id' => $roundVideo['video_id'],
                    'round_id'  =>  $objRound->id
                ];
                if($objRound->name == "Winners")
                {
                    $tempObj = RoundVideos::create($data);
                    $tempObj->save();
                }
               
                $objUser = User::where('id',$roundVideo['user_id'])->first();
                if($objUser != null)
                {
                    $objUser->round         = $objRound->id;
                    $objUser->can_upload    = 1;
                    $objUser->save();
                }
            }

            if($objActiveRoundDetails != null)
            {
                $objActiveRoundDetails->is_active = 0;
                $objActiveRoundDetails->save();
            }
            if($request->has('zoomLink'))
            {
                $objRound->extra_details = $request->zoomLink;
            }else if($request->has('winner_title'))
            {
                foreach($selectedVideosForRound as $roundVideo)
                {
                    $data = [
                        'video_id' => $roundVideo['video_id'],
                        'round_id'  =>  $objRound->id
                    ];
                    $objWinner = new Winners();
                    $objWinner->user_id     = $roundVideo['user_id'];
                    $objWinner->video_id    = $roundVideo['video_id'];
                    $objWinner->title       = $request->winner_title ?? '';
                    $objWinner->details     = $request->winner_details ?? '';
                    $objWinner->save();
                }
               
            }

            $objRound->is_active = 1;
            $objRound->save();
            DB::commit();

            return 'done';
        }catch(\Exception $e)
        {
            DB::rollBack();
            report($e);
            dd($e);
           return 'error'.$e->getMessage();
        }
    }

    public function winnersStore($roundId,Request $request)
    {
        try
        {
            DB::beginTransaction();

            

            return 'done';
        }catch(\Exception $e)
        {
            DB::rollBack();
            report($e);
            dd($e);
           return 'error'.$e->getMessage();
        }
    }
}

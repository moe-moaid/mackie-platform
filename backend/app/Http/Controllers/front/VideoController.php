<?php

namespace App\Http\Controllers\front;

use App\Helpers\GeneralHelpers;
use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\RoundDetails;
use App\Models\RoundVideos;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Video;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    //

    public function show($id)
    {
        $data['title'] = 'Video';
        $objVideo = Video::where('share_link', '=', $id)->first();
        if($objVideo != null)
        {
        	 $objVideo->count += 1;
	        $objVideo->save();
        }
       
        $data['video'] = $objVideo;


        return view('front1.detail', $data);
    }

    public function roundNotStart($roundId)
    {
    	try
    	{
    		$objRoundDetails 	= RoundDetails::with('videos')->where('id',$roundId)->firstOrFail();
    		$objActiveRound		= RoundDetails::with('videos')->where('is_active',1)->firstOrFail();
	        $data['title'] 		= 'Round Details';
	        $data['objRoundDetails'] 		= $objRoundDetails;
	        $data['objActiveRound']	= $objActiveRound;


	        return view('front.roundNotStart', $data);
    	}catch(\Exception $e)
    	{
    		dd($e);
    		return abort(500);
    	}
    }

    


    public function vote(Request $request){

    	if(!request()->has('r'))
    	{
    		$objRoundDetails 	= RoundDetails::with('videos')->where('is_active',1)->first();

    		return redirect()->route('vote',['r' => $objRoundDetails->id]);
    	}

    	if($request->r == 1)
    	{
    	}else if($request->r == 2)
    	{

    	}else if($request->r == 3)
    	{

    	}else if($request->r == 4)
    	{

    	}else if($request->r == 5)
    	{

    	}else if($request->r == 6) //winners
    	{

    	}else
    	{
    		$objCurrentround = RoundDetails::where('is_active',1)->first();
    		return redirect()->route('vote',['r' => $objCurrentround->id ?? 1]);
    	}

    	$objRoundVideos = RoundVideos::with('objVideo','objVideo.genre','objVideo.votes')->where('round_id',$request->r)->get();

    	$objAllRounds 	= RoundDetails::get();
    	if($objRoundVideos->isEmpty())
    	{
    		$objRoundDetails 	= RoundDetails::with('videos')->where('is_active',1)->first();
	        $data['title'] 		= 'Round Details';
	        $data['objRoundDetails']	= $objRoundDetails;
	        $data['objAllRounds']		= $objAllRounds;
	        return view('front.NoVideosFound', $data);
    	}

		$data['title']			='Vote';
		$data['objAllRounds'] 	= $objAllRounds;
		$data['objRoundVideos'] = $objRoundVideos;
		$data['genres']			=Genre::where('status','=',1)->get();
		
		return view('front.vote1', $data);
	}
	public function showvideo(Request $request){
		$video_id=$request->video_id;
		$video=Video::where('id','=',$video_id)->first();
		if($video){
			$message=array('status'=>'200','data'=>$video);
			echo json_encode($message);	
		}
		
	
	}
	public function videoajax(Request $request){
		$value=$request->input('value');
		$html="";
	if($value=='low'){
		$videos=Video::withCount('votes')->get();
		$videos=collect($videos);
		$videos=$videos->sortBy('votes_count',SORT_NATURAL);
		if($videos->isNotEmpty()){
			foreach($videos as $key=>$video){
				$count=$key+1;
				$html.='<div class="col-1-5 col-md-6 iq-mb-30"><div class="epi-box"><div class="epi-img position-relative"> <img style="    height: 200px;width: 280px;" src="'.url("uploads/tumbnail").'/'.$video->tumbnail_image.'" class="img-fluid img-zoom" alt=""><div class="episode-number">'.$count.'</div><div class="episode-play-info"><div class="episode-play"><a href="'.route("play.video",$video->id).'"> <i class="ri-play-fill"></i> </a></div></div></div><div class="epi-desc p-3">
                <div class="d-flex align-items-center justify-content-between">
                <span class="text-white">'.$video->song_name.'</span>                     </div></div>';
						  if(checkVoteCaste($video->id)){
					 $html.='<div class="row"><div class="col-md-6">'.$video->user->name.'</div>
				 <div class="col-md-6"><ul class="list-inline p-0 mt-4 share-icons music-play-lists">
                     <li><span> <i class="far fa-thumbs-up videovote" data-id="{{$video->id}}"></i>
					</span></li>
					</ul></div></div>';
						  }else{
						$html.='<div class="row"><div class="col-md-6">'.$video->user->name.'</div>
				 <div class="col-md-6"><ul class="list-inline p-0 mt-4 share-icons music-play-lists">
					 <li><span><i class="far fa-thumbs-up alreadyvoted" data-id="{{$video->id}}"></i></span></li></ul></div></div>';				 
			}
						   $html.='</div>
						   </div></div></div>';
			}
			$message=array('status'=>'200','data'=>$html);
			echo json_encode($message);
		}else{
			$message=array('status'=>'404','data'=>'<p>no video found</p>');
			echo json_encode($message);	
		}
	}else if($value=="high"){
		$videos=Video::withCount('votes')->get();
		$videos=collect($videos);
		$videos=$videos->sortBy([['votes_count','asc']]);
		
		if($videos->isNotEmpty()){
			foreach($videos as $key=>$video){
				$count=$key+1;
				$html.='<div class="col-1-5 col-md-6 iq-mb-30"><div class="epi-box"><div class="epi-img position-relative"> <img style="    height: 200px;width: 280px;" src="'.url("uploads/tumbnail").'/'.$video->tumbnail_image.'" class="img-fluid img-zoom" alt=""><div class="episode-number">'.$count.'</div><div class="episode-play-info"><div class="episode-play"><a href="'.route("play.video",$video->id).'"> <i class="ri-play-fill"></i> </a></div></div></div><div class="epi-desc p-3">
                <div class="d-flex align-items-center justify-content-between">
                <span class="text-white">'.$video->song_name.'</span>                     </div></div>';
						  if(checkVoteCaste($video->id)){
					 $html.='<div class="row"><div class="col-md-6">'.$video->user->name.'</div>
				 <div class="col-md-6"><ul class="list-inline p-0 mt-4 share-icons music-play-lists">
                     <li><span> <i class="far fa-thumbs-up videovote" data-id="{{$video->id}}"></i>
					</span></li>
					</ul></div></div>';
						  }else{
						$html.='<div class="row"><div class="col-md-6">'.$video->user->name.'</div>
				 <div class="col-md-6"><ul class="list-inline p-0 mt-4 share-icons music-play-lists">
					 <li><span><i class="far fa-thumbs-up alreadyvoted" data-id="{{$video->id}}"></i></span></li></ul></div></div>';				 
			}
						   $html.='</div>
						   </div></div></div>';
			}
			$message=array('status'=>'200','data'=>$html);
			echo json_encode($message);
		}else{
			$message=array('status'=>'404','data'=>'<p>no video found</p>');
			echo json_encode($message);	
		}
		
	}else if($value=='recent'){
		$videos=Video::latest()->get();
			if($videos->isNotEmpty()){
			foreach($videos as $key=>$video){
				$count=$key+1;
				$html.='<div class="col-md-4 iq-mb-30"><div class="epi-box"><div class="epi-img position-relative"> <img style="    height: 200px;width: 280px;" src="'.url("uploads/tumbnail").'/'.$video->tumbnail_image.'" class="img-fluid img-zoom" alt=""><div class="episode-number">'.$count.'</div><div class="episode-play-info"><div class="episode-play"><a href="'.route("play.video",$video->id).'"> <i class="ri-play-fill"></i> </a></div></div></div><div class="epi-desc p-3">
                <div class="d-flex align-items-center justify-content-between">
                <span class="text-white">'.$video->song_name.'</span>                     </div></div>';
						  if(checkVoteCaste($video->id)){
					 $html.='<div class="row"><div class="col-md-6">'.$video->user->name.'</div>
				 <div class="col-md-6"><ul class="list-inline p-0 mt-4 share-icons music-play-lists">
                     <li><span> <i class="far fa-thumbs-up videovote" data-id="{{$video->id}}"></i>
					</span></li>
					</ul></div></div>';
						  }else{
						$html.='<div class="row"><div class="col-md-6">'.$video->user->name.'</div>
				 <div class="col-md-6"><ul class="list-inline p-0 mt-4 share-icons music-play-lists">
					 <li><span><i class="far fa-thumbs-up alreadyvoted" data-id="{{$video->id}}"></i></span></li></ul></div></div>';				 
			}
						   $html.='</div>
						   </div></div></div>';
			}
			$message=array('status'=>'200','data'=>$html);
			echo json_encode($message);
		}else{
			$message=array('status'=>'404','data'=>'<p>no video found</p>');
			echo json_encode($message);	
		}
	}else{
			$videos=Video::where('genre_id','=',$value)->get();
			if($videos->isNotEmpty()){
			foreach($videos as $key=>$video){
				$count=$key+1;
				$html.='<div class="col-md-4 iq-mb-30"><div class="epi-box"><div class="epi-img position-relative"> <img style="    height: 200px;width: 280px;" src="'.url("uploads/tumbnail").'/'.$video->tumbnail_image.'" class="img-fluid img-zoom" alt=""><div class="episode-number">'.$count.'</div><div class="episode-play-info"><div class="episode-play"><a href="'.route("play.video",$video->id).'"> <i class="ri-play-fill"></i> </a></div></div></div><div class="epi-desc p-3">
                <div class="d-flex align-items-center justify-content-between">
                <span class="text-white">'.$video->song_name.'</span>                     </div></div>';
						  if(checkVoteCaste($video->id)){
					 $html.='<div class="row"><div class="col-md-6">'.$video->user->name.'</div>
				 <div class="col-md-6 text-right"><ul class="list-inline p-0 mt-4 share-icons music-play-lists">
                     <li><span> <i class="far fa-thumbs-up videovote" data-id="{{$video->id}}"></i>
					</span></li>
					</ul></div></div>';
						  }else{
						$html.='<div class="row"><div class="col-md-6">'.$video->user->name.'</div>
				 <div class="col-md-6 text-right"><ul class="list-inline p-0 mt-4 share-icons music-play-lists">
					 <li><span><i class="far fa-thumbs-up alreadyvoted" data-id="{{$video->id}}"></i></span></li></ul></div></div>';				 
			}
						   $html.='</div>
						   </div></div></div>';
			}
			$message=array('status'=>'200','data'=>$html);
			echo json_encode($message);
		}else{
			$message=array('status'=>'404','data'=>'<p>no video found</p>');
			echo json_encode($message);	
		}
	}
	}
    public function make_file(Request $request)
    {
        $destination = public_path() . '/uploads/temp/';

        $file_name = "temp_" . date("Ymd-His") . "_" . basename($request->file_name);
        $target_file = $destination . $file_name;
        echo json_encode(array('success' => true, 'filename' => $file_name));
        die;
    }
    public function play(Request $request,$id)
    {
		
		$objVideo = Video::findorfail($id);
		$objVideo->count++;
		$objVideo->save();
		$data['video'] = $objVideo;
		 $data['title'] = 'Play Video';
		 $data['countries'] = GeneralHelpers::countryList();
        return view('front.video-detail', $data);
    }
    
    public function edit($id)
    {
		
        $data['video'] = Video::findorfail($id);
        $data['title'] = 'Edit Video';
        $data['genres'] = Genre::all();
        return view('front.edit', $data);
    }
	public function edit_detail($id){
		$data['video'] = Video::findorfail($id);
        $data['title'] = 'Edit Video';
        $data['genres'] = Genre::all();
        return view('front.edit_detail', $data);
	}
	public function editdetail(Request $request){
		 $video = Video::findorfail($request->video_id);
		$video->genre_id = $request->genre_id;
        $video->song_name = $request->song_name;
		$video->description=$request->description;
        $video->update();
        DB::commit();
        $message = array('message' => 'video description updated successfully', 'code' => 200);
        echo json_encode($message);
        die;
	}
    public function payedit(Request $request)
    {



        $videodata = $request->session()->get('videodata');

        DB::beginTransaction();
        $video = Video::findorfail($request->video_id);
        if ($request->session()->has('videodata')) {
            $video->share_link = preg_replace('/\s+/', '', Auth::guard('user')->user()->name) . $request->song_name . Auth::guard('user')->user()->id . rand(10, 99);
            $video->video = $videodata['videoname'];
			$video->tumbnail_image=$videodata['tumbnailimage'];
            @unlink(public_path() . '/uploads/videos/' . $request->video_name);
        }

        $setting = Setting::findorfail(1);
        $setting->amount = $setting->amount + $request->amount;
        $setting->update();
        $transaction = new Transaction;
        $transaction->pay_by = Auth::guard('user')->user()->name;
        $transaction->paid_id = Auth::guard('user')->user()->id;
        $transaction->reason = 'Edit video';
        $transaction->amount = $request->amount;
		$transaction->video_id=$request->video_id; 
        $transaction->save();
        $video->genre_id = $request->genre_id;
        $video->count = $video->count + 1;
        $video->song_name = $request->song_name;
		$video->description=$request->description;
        $video->update();
        DB::commit();
        $message = array('message' => 'video uploaded successfully', 'code' => 200);
        echo json_encode($message);
        die;
    }

    public function update(Request $request)
    {
        $rules = [
            'song_name' => 'required',
            'genre' => 'required',
			'description'=>'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $videodata = $request->session()->get('videodata');

        $userid = Auth::guard('user')->user()->id;
        DB::beginTransaction();
        $video = Video::findorfail($request->video_id);
        if ($request->session()->has('videodata')) {
            $video->share_link = preg_replace('/\s+/', '', Auth::guard('user')->user()->name) . $request->song_name . Auth::guard('user')->user()->id . rand(10, 99);
            $video->video = $videodata['videoname'];
			$video->tumbnail_image=$videodata['tumbnailimage'];
            @unlink(public_path() . '/uploads/videos/' . $request->video_name);
        }
        $video->genre_id = $request->genre;

        $video->count = $video->count + 1;
        $video->song_name = $request->song_name;
		$video->description=$request->description;
        $video->update();
        DB::commit();
        Session::flash('success', 'video updated successfully');
        Session::forget('videodata');
        return back();
    }
    public function store(Request $request)
    {
        try
        {
        	$rules = [
	            'song_name' => 'required',
	            'genre' => 'required',
				'description'=>'required',
	        ];
	        $validator = Validator::make($request->all(), $rules);
	        if ($validator->fails()) {
	            return back()->withErrors($validator)->withInput();
	        }
	        $videodata = $request->session()->get('videodata');
	        $userid = Auth::guard('user')->user()->id;
	        if(!Auth::guard('user')->user()->eligibleForUploadingVideo())
	        {
		        Session::flash('success', "Sorry You Cannot Upload more Videos in This Round ");
	        	return redirect()->back();
	        }
			
			if($userid)
			{
		        if ($userid == $videodata['user_id']) 
		        {
				
					 $destinationPath =public_path('uploads/tumbnail');
					// $tumbnail_image = $request->file('tumbnail_image');
					// $filename=rand(1, 999) . time() . '.'.$tumbnail_image->getClientOriginalExtension();
					// $tumbnail_image->move($destinationPath, $filename);
		            DB::beginTransaction();
		            $video = new  Video;
		            $video->user_id 		= Auth::guard('user')->user()->id;

		            $tempSlug 				= $this->SeoFriendlySlug(Auth::guard('user')->user()->id . $request->song_name . Auth::guard('user')->user()->id . rand(10, 99));
		            $video->share_link 		= Str::slug($tempSlug, '-');
		            $video->genre_id 		= $request->genre;
					$video->description 	= $request->description;
		            $video->video 			= $videodata['videoname'];
					$video->tumbnail_image 	= $videodata['tumbnailimage'];
		            $video->song_name 		= $request->song_name;
		            $video->save();

		            $objActiveRoundDetails = RoundDetails::where('is_active',1)->first();
		            if($objActiveRoundDetails !=null)
		            {
		            	$data = [
		                    'video_id' 	=> $video->id,
		                    'round_id'  =>  $objActiveRoundDetails->id
		                ];
		                $tempObj = RoundVideos::create($data);
		                $tempObj->save();
		            }
		            $this->updateUserCanUploadVideo();	
		            DB::commit();
		            Session::flash('success', 'video uploaded successfully');
		            return back();
		        } else 
		        {
		            Session::flash('warning', 'sorry try again');
		            return back();
		        }
			}else{
				  return redirect()->route('user.login');
			}
        }catch(\Exception $e)
        {
	        Session::flash('success', $e->getMessage());
        	return back();

        }
    }
    public function updateUserCanUploadVideo()
    {
    	$objUser = Auth::guard('user')->user();
    	$objRoundDetails = RoundDetails::with('videos')->where('is_active',1)->first();
        if($objRoundDetails != null)
        {
            $userCount = 0;
            foreach ($objRoundDetails->videos as $key => $objRoundVideos) 
            {
                if($objRoundVideos->user->id == $objUser->id)
                {
                    $userCount++;
                }
            }
            if($userCount > 1)
            {
            	$objUser->can_upload = 0;
            }else
            {
            	$objUser->can_upload = 0;
            }
            $objUser->save();
        }
    }
    public  function SeoFriendlySlug($slug)
    {
        $stopWords = ['A','ABOUT','ACTUALLY','ALMOST','ALSO','ALTHOUGH','ALWAYS','AM','AN','AND','ANY','ARE','AS','AT','BE','BECAME','BECOME','BUT','BY','CAN','COULD','DID','DO','DOES','EACH','EITHER','ELSE','FOR','FROM','HAD','HAS','HAVE','HENCE','HOW','I','IF','IN','IS','IT','ITS','JUST','MAY','MAYBE','ME','MIGHT','MINE','MUST','MY','MINE','MUST','MY','NEITHER','NOR','NOT','OF','OH','OK','WHEN','WHERE','WHEREAS','WHEREVER','WHENEVER'];

        foreach ($stopWords as $word) 
        {
            $slug = str_replace($word, "", $slug);
        }
        return $slug;
    }
}

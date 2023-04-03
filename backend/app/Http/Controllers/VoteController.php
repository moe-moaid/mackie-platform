<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Vote;
use App\Models\RoundDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class VoteController extends Controller
{
    //
    public function apealFormIndex()
    {
        try
        {
            $objUser            = Auth::guard('user')->user();
            $objActiveRound             = RoundDetails::with('videos')->where('is_active',1)->firstOrFail();
            if($objUser->can_upload == 1)
            {
                Session::flash('success', 'You already can upload Video In This round.Please go to dashboard and upload video');
                return redirect()->route('vote',['r' => $objActiveRound->id]);
            }

            $data['title']              = 'Appeal Form For Uploading Video in '.$objActiveRound->name;
            $data['objUser']            = $objUser;
            $data['objActiveRound']     = $objActiveRound;


            return view('front.appealForm', $data);
        }catch(\Exception $e)
        {
            dd($e);
            return abort(500);
        }
    }

    public function index(Request $request)
    {
        $video_id = $request->video_id;
        if (session()->get('visitordata')) {
            $visitordata        = session()->get('visitordata');
			$user_id            = $visitordata['visitor_id'];
            $objRoundDetails    = RoundDetails::where('is_active',1)->first();
            $checkvote          = Vote::where('user_id', '=', $user_id)->where('video_id', '=', $video_id)->where('round_id',$objRoundDetails->id ?? 1)->first();
            
            if ($checkvote == null) {
                DB::beginTransaction();
                $setting = Setting::findorfail(1);
                $setting->amount = $setting->amount + $request->amount;
                $setting->update();
                $transaction = new Transaction;
       
                $transaction->paid_id = $user_id;
				$transaction->video_id=$request->video_id;
                $transaction->amount = $request->amount;
                $transaction->save();

                $vote               = new Vote;
                $vote->user_id      = $user_id;
                $vote->video_id     = $request->video_id;
                $vote->round_id     = $objRoundDetails->id ?? 1;
                $vote->save();

                DB::commit();
                $message = array('message' => 'your vote is successfully voted', 'status' => 200);
                echo json_encode($message);
                die;
            } else {
                $message = array('message' => 'you have already voted', 'status' => 401);
                echo json_encode($message);
                die;
            }
        } else {
            $message = array('message' => 'you have to login to give vote', 'status' => 400);
            echo json_encode($message);
            die;
        }
    }
}

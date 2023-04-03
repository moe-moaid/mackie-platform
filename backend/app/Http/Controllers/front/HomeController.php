<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Helpers\GeneralHelpers;
class HomeController extends Controller
{
    public function index()
    {
        $videos = Video::orderBy('created_at','DESC')->limit(50)->get();
		$data['countries']=getAllCountries();
		// $data['countries'] =json_decode($data['countries'],True);
		 $data['codes']=getPhoneCode(); 
		$data['codes']=json_decode($data['codes'],True);
		$data['videos']=$videos;
        $data['title'] = 'Home';
	
        return view('front.index', $data);
    }

    public function faq()
    {
        $data['title'] = 'FAQ - Frequently Asked Questions';
        return view('front.faq', $data);
    }

    public function privacyPolicy()
    {
        $data['title'] = 'Privacy Policy';
        return view('front.privacyPolicy', $data);
    }
    public function howItWorks()
    {
        $data['title'] = 'How it Works';
        return view('front.howItWorks', $data);
    }

    public function brand()
    {
        $data['title'] = 'Brand';
        return view('front.brand', $data);
    }

    public function rules()
    {
        $data['title'] = 'Rules ';
        return view('front.rules', $data);
    }

    public function voting()
    {
        $data['title'] = 'Voting  ';
        return view('front.voting', $data);
    }

}

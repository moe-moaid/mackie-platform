<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Video;
use App\Models\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
class AboutController extends Controller
{
    public function index()
    {
        $data['title'] = 'About Us';
        return view('front.about', $data);
    }
	
	
	
	
	
	
	
}
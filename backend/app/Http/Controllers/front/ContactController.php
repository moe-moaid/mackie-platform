<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Video;
use App\Models\Query;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
class ContactController extends Controller
{
    public function index()
    {
      
        
        $data['title'] = 'Conact Us';
        return view('front.contact', $data);
    }
	public function store(Request $request){
		$rules=[
		    'name'=>'required',
			'email'=>'required|email',
			'message'=>'required',
		
		];
		$validator=Validator::make($request->all(),$rules);
		if($validator->fails()){
			return back()->withErrors($validator)->withInput();
		}
		$contact=new Query;
		$contact->email=$request->email;
		$contact->name=$request->name;
		$contact->message=$request->message;
		$contact->save();
		 Session::flash('success', 'Admin will contact to you ASAP');
		  return back();
	}
}

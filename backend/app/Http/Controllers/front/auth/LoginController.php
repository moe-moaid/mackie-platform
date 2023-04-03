<?php

namespace App\Http\Controllers\front\auth;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\User;
use App\Models\Outsider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use PragmaRX\Countries\Package\Countries;
use Illuminate\Support\Facades\Validator;
use App\Helpers\GeneralHelpers;
use Artisan;
use Illuminate\Validation\ValidationException;
class LoginController extends Controller
{
    public function index()
    {
        $data['title'] = 'User Login';

        return view('front.login', $data);
    }
	public function clear(){
		Artisan::call('cache:clear');
      Artisan::call('config:clear');
	  Artisan::call('config:cache');
	  
      Artisan::call('route:clear');
      Artisan::call('view:clear');
	  
	  dd('clear');
	}
     public function logout()
    {
        
        Auth::guard('user')->logout();
        return redirect()->route('/');
    }
	public function userstore(Request $request){
		$email=Auth::guard('user')->user()->email;
		
		$check=Outsider::where('email','=',$email)->first();
		if($check==null){
			$vistor=new Outsider;
			$vistor->email=$email;
			$vistor->save();
			$visitor_id=$vistor->id;
			$session_array = ['visitor_id' => $visitor_id];
            Session::put('visitordata', $session_array);
			$result=array('status'=>200);
			echo json_encode($result);
		}else{
			
				$visitor_id=$check->id;
			$session_array =['visitor_id' => $visitor_id];
            Session::put('visitordata', $session_array);
			$result=['status'=>200];
			echo json_encode($result);
		}
	}
	public function visitor(Request $request){
		$email=$request->email;
	
		$check=Outsider::where('email','=',$email)->first();
		if($check==null){
			$vistor=new Outsider;
			$vistor->email=$request->email;
			$vistor->save();
			$visitor_id=$vistor->id;
			$session_array = ['visitor_id' => $visitor_id];
            Session::put('visitordata', $session_array);
			$result=array('status'=>200);
			echo json_encode($result);
		}else{
			
				$visitor_id=$check->id;
			$session_array =['visitor_id' => $visitor_id];
            Session::put('visitordata', $session_array);
			$result=['status'=>200];
			echo json_encode($result);
		}
	}
	public function manage(){
		
		$user_id = Auth::guard('user')->user()->id;
		$data['title']='manage-profile';
		 $data['countries']=getAllCountries();
			 $data['countries'] =json_decode($data['countries'],True);
			 	 $data['codes']=getPhoneCode(); 
		$data['codes']=json_decode($data['codes'],True);
		$data['user']=User::find($user_id);
		return view('front.manage-profile', $data);
	}
	public function storeprofile(Request $request){
		if ($request->file('image')) {
             $rules = [
            'name' => 'required',
            'age' => 'required',
            'phone' => 'required',
            'address' => 'required',
			'country'=>'required',
			'code'=>'required',
			'bio'=>'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsg = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

            $user = User::findOrFail($request->user_id);
            if($request->file('image'))
            {
                $objFile    = GeneralHelpers::FileUploader('users',$request->image,'rand');
                $user->image = $objFile['uploaded_path'];
            }
            $user->name = $request->name;
            $user->age = $request->age;
			$user->code = $request->code;
            if($request->has('facebook'))
            {
                $user->facebook=$request->facebook;
            }

            if($request->has('youtube'))
            {
                $user->youtube=$request->youtube;
            }

            if($request->has('instagram'))
            {
                $user->instagram=$request->instagram;
            }

            if($request->has('tiktok'))
            {
                $user->tiktok=$request->tiktok;
            }




			$user->phone = $request->phone;
			$user->address=$request->address;
			$user->bio=$request->bio;
			$user->country=$request->country;
            $user->save();
            @unlink('uploads/users/' . $request->old_image);
            $request->session()->flash('success', 'user profile update successfully');

            return redirect()->route('manage.profile');
            // return 'success';
        } else {
             $rules = [
            'name' => 'required',
            'age' => 'required',
            'phone' => 'required',
            'address' => 'required',
			'country'=>'required',
			'code'=>'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsg = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
            $user = User::findOrFail($request->user_id);
            $user->name = $request->name;
            $user->age = $request->age;
            if($request->has('facebook'))
            {
                $user->facebook=$request->facebook;
            }

            if($request->has('youtube'))
            {
                $user->youtube=$request->youtube;
            }

            if($request->has('instagram'))
            {
                $user->instagram=$request->instagram;
            }

            if($request->has('tiktok'))
            {
                $user->tiktok=$request->tiktok;
            }
            
			$user->code = $request->code;
			$user->phone = $request->phone;
			$user->address=$request->address;
            if($request->has('bio'))
            {
                $user->bio=$request->bio;
            }
			$user->country=$request->country;
            $user->save();
			   $request->session()->flash('success', 'user profile update successfully');
            return redirect()->route('manage.profile');
        // return 'success';
        }
	}
    public function signup()
    {     $data['countries']=getAllCountries();
		 $data['countries'] =json_decode($data['countries'],True);
		 $data['codes']=getPhoneCode(); 
		$data['codes']=json_decode($data['codes'],True);
        $data['title'] = 'Sing up';
  
        return view('front.signup', $data);
    }

    public function postLogin(Request $request)
    {
        try
        {
             $rules = [
                'email' => 'required|email',
                'password' => 'required',
            ];
            $validator = validator::make($request->all(), $rules);
            if ($validator->fails()) 
            {
                  $errmsg = $validator->getMessageBag()->add('error', 'true');
                  throw new \Exception($errmsg, 1);
                  
            }
            $isloggedIn = false;
            $check = User::where('email', '=', $request->email)->first();
            if ($check) 
            {
                if ($check->status != 0) 
                {
                    if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])) 
                    {
                        $isloggedIn = true;
                    } else 
                    {
                         $request->session()->flash('warning', 'your credentials not matched with our data');
                       
                    }
                } else 
                {
                    
                     $request->session()->flash('warning', 'your account is still not approved'); 
                }
            } else
             {
                $request->session()->flash('warning', 'Email does not exist'); 
               
            }

            if($isloggedIn)
            {
                return redirect()->route('user.dashboard');
            }else
            {
                throw new \Exception("Sorry You cannot Login", 1);
                
            }

        }catch(\Exception $e)
        {

            $request->session()->flash('warning', $e->getMessage());
            return redirect()->back();
        }
    }
	public function ajaxlogin(Request $request){
		 $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $validator = validator::make($request->all(), $rules);
        if ($validator->fails()) 
        {
              $errmsg = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
		$check = User::where('email', '=', $request->email)->first();
        if ($check) 
        {

            if ($check->status != 0) {
                if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])) {
                    return redirect()->route('user.dashboard');
                } else {
					 $request->session()->flash('warning', 'your credentials not matched with our data');
                 return 'warning';
                   
                }
            } else {
                
				 $request->session()->flash('warning', 'your account is still not approved');
                 return 'warning';
            }
        } else
         {
 $request->session()->flash('warning', 'Email does not exist');
        return 'warning';
           
        }
		 
	}
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $validator = validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $check = User::where('email', '=', $request->email)->first();
        if ($check) {

            if ($check->status != 0) {
                if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])) {
                    return redirect()->route('user.dashboard');
                } else {
                    $request->session()->flash('warning', 'your credentials not matched with our data');
                    return back();
                }
            } else {
                $request->session()->flash('warning', 'your account is still not approved');
                return back();
            }
        } else {

            Session::flash('warning', 'Email does not exist');
            return back();
        }
    }

    public function register(Request $request)
    {
        try
        {
            $rules = [
                'fname'         => 'required',
                'email'         => 'required|email|unique:users,email',
                'password'      => 'required|confirmed',
                'password_confirmation'=>'required',
                'age'           => 'required',
                'phone'         => 'required',
                'address'       => 'required',
                'country'       =>'required',
                'code'          =>'required',
                'image'         =>'sometimes',
            ];

           
        
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) 
            {
                throw new ValidationException($validator);             
            }
           
            DB::beginTransaction();
            $user               = new User;
            $user->name         = $request->fname;
            $user->email        = $request->email;
            $user->address      = $request->address;
            $user->phone        = $request->phone;
            $user->age          = $request->age;

            if($request->has('image'))
            {
                if($request->file('image'))
                {
                    $objFile    = GeneralHelpers::FileUploader('users',$request->image,'rand');
                    $user->image = $objFile['uploaded_path'];
                }
                
            }
            $user->status         =1;
            $user->code           =$request->code;
            $user->country        =$request->country;
            $user->role           = 2;
            $user->password       = Hash::make($request->password);


            $user->save();
            DB::commit();
            $request->session()->flash('success', 'user created successfully');

            return redirect()->route('user.login');
        }catch(ValidationException $e)
        {
            $errors = [];
            foreach ($e->errors() as $key => $objError) {
                $errors[] = $key ." / ". implode(",", $objError);
            }

            $request->session()->flash('success', $errors[0]);
            return redirect()->route('user.signup');
             
        }
        catch(\Exception $e)
        {
            $request->session()->flash('success', $e->getMessage());
            return redirect()->route('user.signup');
        }
    }
	public function test(Request $request)
    {
		try
        {
            $rules = [
                'fname'         => 'required',
                'email'         => 'required|email|unique:users,email',
                'password'      => 'required|confirmed',
                'password_confirmation'=>'required',
                'age'           => 'required',
                'phone'         => 'required',
                'address'       => 'required',
                'country'       =>'required',
                'code'          =>'required',
                'image'         =>'sometimes',
            ];

           
        
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) 
            {
                throw new ValidationException($validator);             
            }
           
            DB::beginTransaction();
            $user               = new User;
            $user->name         = $request->fname;
            $user->email        = $request->email;
            $user->address      = $request->address;
            $user->phone        = $request->phone;
            $user->age          = $request->age;

            if($request->has('image'))
            {
                $image = $request->file('image');
                $fileArray = array('image' => $image);
                $rules = array(
                    'image' => 'required|mimes:jpeg,gif,png,jpg' // max 10000kb
                );
                $validator = Validator::make($fileArray, $rules);
                if ($validator->fails()) 
                {

                    return back()->withErrors($validator) ->withInput();
                }
                $destinationPath = public_path('uploads/users');
                $filename =  rand(1, 999) . time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $filename);
                $user->image    = $filename;
            }
            $user->status         =1;
            $user->code           =$request->code;
            $user->country        =$request->country;
            $user->role           = 2;
            $user->password       = Hash::make($request->password);

            $user->save();
            DB::commit();
            $request->session()->flash('success', 'user created successfully');
            
            return 'success';
        }catch(ValidationException $e)
        {
            $errors = [];
            foreach ($e->errors() as $key => $objError) {
                $errors[] = $key ." / ". implode(",", $objError);
            }

            return response()->json($errors, 422);
             
        }
        catch(\Exception $e)
        {
            return response()->json($e->getMessage(), 422);
        }
        
	}
    public function store12(Request $request)
    {
        $rules = [
            'fname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed',
			'password_confirmation'=>'required',
            'age' => 'required',
            'phone' => 'required',
            'address' => 'required',
			'country'=>'required',
			'code'=>'required',
			'image'=>'required',
        ];

       
		
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsg = $validator->getMessageBag()->add('error', 'true');
                return back()->withErrors($validator) ->withInput();       
        }
        $image = $request->file('image');
        $fileArray = array('image' => $image);
        $rules = array(
            'image' => 'required|mimes:jpeg,gif,png,jpg' // max 10000kb
        );
        $validator = Validator::make($fileArray, $rules);
        if ($validator->fails()) 
        {

            return back()->withErrors($validator) ->withInput();
        }
        $destinationPath = public_path('uploads/users');
        $filename =  rand(1, 999) . time() . '.' . $image->getClientOriginalExtension();
        $image->move($destinationPath, $filename);
        DB::beginTransaction();
        $user = new User;
        $user->name = $request->fname;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->age = $request->age;
        $user->image = $filename;
		$user->status=1;
		$user->code=$request->code;
		$user->country=$request->country;
        $user->role = 2;
        $user->password = Hash::make($request->password);
        $user->save();
        DB::commit();
        $request->session()->flash('success', 'user created successfully');
        
        return 'success';
    }
}

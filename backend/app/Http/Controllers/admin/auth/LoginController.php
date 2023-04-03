<?php

namespace App\Http\Controllers\admin\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        $data['title'] = __('file.admin_login');
        return view('admin.login', $data);
    }
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => 1], $request->remember)) {
            $data['activePage'] = 'dashboard';
            $data['title'] = __('file.dashboard');
            return redirect()->route('dashboard')->with($data);
        } else {

            $request->session()->flash('warning', 'Your email or password is incorrect');
            return back()->withInput();;
        }
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route();
    }
}

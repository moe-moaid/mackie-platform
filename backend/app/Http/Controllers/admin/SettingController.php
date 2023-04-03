<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $data['activePage'] = 'settings';
        $data['title'] = 'Settings';
        $data['setting'] = Setting::first();
        return view('admin.setting.index', $data);
    }
    public function stage(Request $request)
    {
    }
    public function status(Request $request)
    {
        $setting = Setting::find($request->setting_id);
        if ($setting) {
            $setting->is_upload = $request->status;
            $setting->update();
            $message = array('message' => 'status updated successfully', 'status' => 200);
            echo json_encode(($message));
        } else {
            $message = array('message' => 'sorry try again', 'status' => 404);
            echo json_encode(($message));
        }
    }
}

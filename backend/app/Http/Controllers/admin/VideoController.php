<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    //
    public function index()
    {
        $data['activePage'] = 'video';
        $data['title'] = 'video';
        $data['songs'] = Video::all();
        return view('admin.video.index', $data);
    }
}

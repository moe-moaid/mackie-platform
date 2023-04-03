<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    //

    public function index($id)
    {
        $data['title'] = 'Genre';
        $data['videos'] = Video::where('genre_id', '=', $id)->get();
        $data['genre'] = Genre::findorfail($id);
        return view('front1.genre', $data);
    }
}

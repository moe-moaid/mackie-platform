<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $data['activePage'] = 'genres';
        $data['title'] = 'Genres';
        $data['genres'] = Genre::all();
        return view('admin.genre.index', $data);
    }
    public function store(Request $request)
    {
        $rules = [
            'genre' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }
        DB::beginTransaction();
        $genre = Genre::create([
            'genre_name' => $request->genre,
        ]);

        DB::commit();

        $request->session()->flash('success', 'genre created successfully');
        return 'success';
    }
    public function status(Request $request)
    {
        $genre = Genre::find($request->genre_id);
        if ($genre) {
            $genre->status = $request->status;
            $genre->update();
            $message = array('message' => 'status updated successfully', 'status' => 200);
            echo json_encode(($message));
        } else {
            $message = array('message' => 'sorry try again', 'status' => 404);
            echo json_encode(($message));
        }
    }
    public function delete(Request $request)
    {

        $genre = Genre::findorfail($request->genre_id);
        $genre->delete();
        Session::flash('success', 'genre deleted successfully!');
        return back();
    }
}

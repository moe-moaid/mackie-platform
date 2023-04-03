<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function status(Request $request)
    {

        $user = User::find($request->user_id);
        if ($user) {
            $user->status = $request->status;
            $user->update();
            $message = array('message' => 'status updated successfully', 'status' => 200);
            echo json_encode(($message));
        } else {
            $message = array('message' => 'sorry try again', 'status' => 404);
            echo json_encode(($message));
        }
    }
}

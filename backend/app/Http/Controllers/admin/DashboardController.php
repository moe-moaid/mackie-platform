<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Models\Transaction;
use App\Models\Vote;
use App\Models\RoundDetails;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function index()
    {

        $objUsers           = User::get();
        $objVideos          = Video::get();
        $objTransactions    = Transaction::get();
        $objVotes           = Vote::get();
        $objRoundDetails    = RoundDetails::get();

        $data = [
            'title'             => 'Dashboard',
            'activePage'        => 'dashboard',
            'objUsers'          => $objUsers,
            'objVideos'         => $objVideos,
            'objTransactions'   => $objTransactions,
            'objVotes'          => $objVotes,
            'objRoundDetails'   => $objRoundDetails,
        ];

        
        return view('admin.index', $data);
    }
    public function users(Request $request)
    {
        $data['activePage'] = 'users';
        $data['title'] = 'Users';
        $data['users'] = User::where('role', '=', 2)->get();
        return view('admin.users.index', $data);
    }
    public function test()
    {

        return view('admin.test2');
    }
}

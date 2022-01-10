<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\User;

class PageController extends Controller
{
    public function home()
    {
        if (Session::get('locale') !== null) {
            $language = Session::get('locale');
        } else {
            $language = 'en';
        }

        return view('homepage', compact('language'));
    }

    public function topUsers()
    {
        $users = User::has('messages')->withCount(['messages' => function($query) {
            $query->whereNull('parent_id');
        }])->take(10)->get();

        return view('top-users', compact('users'));
    }
}

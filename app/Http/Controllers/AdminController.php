<?php

namespace App\Http\Controllers;

use App\Log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public const DASHBOARD = '/admin/dashboard';

    public function dashboard()
    {
        if (Session::get('admin') !== 1) {
            return abort(403);
        }

        $users = User::latest()->paginate(20);

        return view('admin.dashboard', compact('users'));
    }

    public function logs(Request $r)
    {
        $logs = Log::with('user');
        $type = $r->input('type');
        $user_name = $r->input('user_name');

        if ($type) {
            $logs = $logs->where('type', $type);
        }
        
        if ($user_name) {            
            if ($user_name == 'system') {
                $logs = $logs->whereNull('user_id');
            } else {
                $logs = $logs->whereHas('user', function($query) use ($user_name) {
                    $query->where('name', 'like', '%' . $user_name . '%');
                });
            }
        }

        $logs = $logs->latest()->paginate(50);

        return view('admin.logs', compact('logs'));
    }

    public function messages()
    {
        $messages = \App\Contact::paginate(10);

        return view('admin.messages', compact('messages'));
    }

    public function logout()
    {
        Session::forget('admin');

        return redirect('/');
    }
}

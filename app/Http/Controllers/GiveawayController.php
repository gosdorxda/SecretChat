<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use App\ViewLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiveawayController extends Controller
{
    public function __invoke()
    {
        $rankings = [];
        $users = User::all();

        foreach ($users as $i => $u) {
            $rankings[$i] = $u;
            $rankings[$i]->messages_count = $u->parentMessages()->count();
            $rankings[$i]->views_count = $u->views()->count();
            $rankings[$i] = $rankings[$i]->toArray();
        }

        $rankings = collect($rankings)->sortByDesc(function ($ranking) {
            return $ranking['views_count'] + $ranking['messages_count'] * 50;
        })->slice(0, 15);

        return view('giveaway', compact('rankings'));
    }
}

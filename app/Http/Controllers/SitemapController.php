<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $users = User::latest()->get();
        $usersArray = [];

        foreach ($users as $user) {
            array_push($usersArray, config('app.url') . '/' . $user->id);
        }

        return response(join("\n", $usersArray), 200)
            ->header('Encoding', 'UTF-8')
            ->header('Content-Type', 'text/plain');
    }
}

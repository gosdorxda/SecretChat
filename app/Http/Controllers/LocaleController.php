<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    public function __invoke(Request $request, $locale = 'id')
    {
        $request->session()->put('locale', $locale);

        return redirect()->back();
    }
}

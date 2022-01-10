<?php

use App\Fonnte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes(['login' => false]);
Route::get('/login', 'PageController@home')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/login/{provider}', 'Auth\LoginController@redirectToProvider');
Route::get('/login/{provider}/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('/sitemap.txt', 'SitemapController');

Route::get('/top-users', 'PageController@topUsers');
Route::get('/contact', 'ContactController@create');
Route::post('/contact', 'ContactController@store');

Route::delete('/contacts/{contact}/delete', 'ContactController@destroy');
Route::get('/admin/dashboard', 'AdminController@dashboard');
Route::get('/admin/logs', 'AdminController@logs');
Route::get('/admin/messages', 'AdminController@messages');
Route::post('/admin/logout', 'AdminController@logout');

Route::get('/', 'PageController@home');
Route::get('/locale/{language}', 'LocaleController');
Route::get('/giveaway', 'GiveawayController');

Route::get('/{user}', 'MessageController');
Route::get('/{locale}/{user}', 'MessageController@local');

Route::post('/messages', 'MessageController@store');

Route::group(['middleware' => ['auth']], function () {
    Route::delete('/messages/{id}', 'MessageController@destroy');

    Route::post('/notification/send', 'NotificationController@send');
    Route::post('/notification/validate', 'NotificationController@validateCode');
    Route::put('/notification/update', 'NotificationController@update');
});

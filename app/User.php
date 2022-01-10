<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function parentMessages()
    {
        return $this->hasMany('App\Message')
            ->whereNull('parent_id')
            ->where('created_at', '>', Carbon::now()->subDays(7));
    }

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function views()
    {
        return $this->hasMany('App\ViewLog', 'user_id')
            ->where('view_at', '>', Carbon::now()->subDays(7));
    }
}

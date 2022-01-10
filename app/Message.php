<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Stevebauman\Purify\Purify;

class Message extends Model
{
    public $guarded = [];

    public function getTimeAgoAttribute()
    {
        return Carbon::parse($this->created_at)->locale(Session::get('locale'))->diffForHumans();
    }

    public function getContentAttribute($value)
    {
        return (new Purify)->clean(str_replace('\n', '<br/>', $value));
    }

    public function replies()
    {
        return $this->hasMany('App\Message', 'parent_id');
    }
}

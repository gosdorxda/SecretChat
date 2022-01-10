<?php

namespace App\Http\Controllers;

use App\Message;
use App\Notifications\NewMessageMailNotification;
use App\Notifications\NewMessageWhatsappNotification;
use App\User;
use App\ViewLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MessageController extends Controller
{
    public function __construct()
    {
        if (Session::get('locale') !== null) {
            App::setLocale(Session::get('locale'));
        } else {
            App::setLocale('en');
        }
    }

    public function __invoke(User $user)
    {
        return $this->initMessageView($user);
    }

    public function local($locale = 'en', User $user)
    {
        return $this->initMessageView($user, $locale);
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_owner' => 'nullable|boolean',
            'parent_id' => 'nullable|exists:messages,id',
            'content' => 'required|max:65535'
        ]);

        $new_message = Message::create($v);

        $owner = User::find($v['user_id']);

        if ($new_message) {
            if ($owner->email != null && $owner->email_notification) {
                $owner->notify(new NewMessageMailNotification($new_message->id));
            }

            if ($owner->whatsapp_notification) {
                $owner->notify(new NewMessageWhatsappNotification($new_message->id));
            }
        }

        return $request->wantsJson()
            ? [
                'success' => $new_message,
                'data' => [
                    'id' => $new_message->id,
                    'time_ago' => $new_message->time_ago,
                    'is_owner' => $new_message->is_owner,
                    'content' => $new_message->content,
                ]
            ]
            : redirect()->back()->with('sent', true);
    }

    public function destroy($id)
    {
        $message = Message::find($id);

        if ($message) {
            $parent = $message->parent_id == null;

            $message->delete();
        }

        return [
            'success' => true,
            'parent' => isset($parent) ? $parent : false
        ];
    }

    private function initMessageView(User $user, $locale = 'en')
    {
        $this->addViewLog($user->id);

        $this->abortIfLocaleIsNotExists($locale);

        Session::put('locale', $locale);

        App::setlocale($locale);

        $chart_labels = [];
        $chart_data = [];
        $views = $user->views()->select('ip')->groupBy(['ip', 'view_at'])->get()->count();

        $messages = $user->messages()->whereNull('parent_id')->with('replies');

        $chart_stats = $user->views()
            ->select(DB::raw('day(view_at) as day_at, count(*) as msg_count'))
            ->orderBy('view_at', 'asc')
            ->groupBy('view_at')
            ->get();

        foreach ($chart_stats as $l7) {
            array_push(
                $chart_labels,
                $l7->day_at . ' ' . \Carbon\Carbon::parse($l7->created_at)->monthName
            );
            array_push($chart_data, $l7->msg_count);
        }

        return view('message', compact('user', 'views', 'messages', 'chart_labels', 'chart_data'));
    }

    private function abortIfLocaleIsNotExists($locale)
    {
        $dir    = '../resources/lang';
        $locales = array_slice(scandir($dir), 2);

        if (!in_array($locale, $locales)) abort(404);
    }

    private function addViewLog($user_id)
    {
        try {
            if (auth()->check()) {
                $auth_user = User::findOrFail(auth()->user()->id);

                if ($auth_user != $user_id) {
                    $view_log = new ViewLog;
                    $view_log->user_id = $user_id;
                    $view_log->ip = $this->getIp();
                    $view_log->save();
                }
            } else {
                $view_log = new ViewLog;
                $view_log->user_id = $user_id;
                $view_log->ip = $this->getIp();
                $view_log->save();
            }
        } catch (\Exception $e) {
        }
    }

    private function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // whether ip is from proxy
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // whether ip is from remote address
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        return $ip_address;
    }
}

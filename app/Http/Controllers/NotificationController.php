<?php

namespace App\Http\Controllers;

use App\Fonnte;
use App\Log;
use App\Mail\NotificationVerification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    public function send(Request $request, Fonnte $fonnte)
    {
        $v = $request->validate([
            'type' => 'required|max:10',
            'email' => 'nullable|max:190',
            'whatsapp' => 'nullable|max:20',
        ]);

        $user = User::findOrFail(auth()->user()->id);

        if ($v['type'] == 'email') {
            $user->email_notification_activation = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->save();

            try {
                $log = new Log;

                if (auth()->check()) {
                    $log->user_id = auth()->user()->id;
                }

                $log->type = 'success';

                $log->content = 'verification mail has been sent to SMTP server';

                $log->save();

                Mail::to($v['email'])->queue(new NotificationVerification($user));

                return [
                    'success' => true,
                    'message' => __('An email will be sent to your email address, please check it out and enter the code below to confirm the address!')
                ];
            } catch (\Exception $e) {
                $log = new Log;

                if (auth()->check()) {
                    $log->user_id = auth()->user()->id;
                }

                $log->type = 'error';

                $log->content = 'verification email can\'t be sent to SMTP server';

                $log->save();

                return [
                    'success' => false,
                    'message' => __("Email can't be sent, please try again or contact admin!")
                ];
            }
        } elseif ($v['type'] == 'whatsapp') {
            try {
                $user->whatsapp_notification_activation = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->save();

                $result = $fonnte->to($v['whatsapp'])
                    ->send('Hello, ' . $user->name . '!\n\nPlease enter *' . $user->whatsapp_notification_activation . '* into the ' . config('app.name') . '\'s pop up to confirm your number!');

                if ($result->status == true) {
                    return [
                        'success' => $result->status,
                        'message' => __('A chat will be sent to your Whatsapp. Please check it out and enter the code below to confirm the number!')
                    ];
                } else {
                    return [
                        'success' => $result->status,
                        'message' => __('Whatsapp chat can\'t be sent to your number, please make sure the number is correct or contact the admin!')
                    ];
                }
            } catch (\Throwable $th) {
                $log = new Log;

                if (auth()->check()) {
                    $log->user_id = auth()->user()->id;
                }

                $log->type = 'error';

                $log->content = 'curl to Whatsapp API is failed';

                $log->save();

                return [
                    'success' => false,
                    'message' => __('Whatsapp chat can\'t be sent to your number, please make sure the number is correct or contact the admin!')
                ];
            }
        }

        return abort(404);
    }

    public function update(Request $request)
    {
        $v = $request->validate([
            'email_notification' => 'nullable|max:5',
            'whatsapp_notification' => 'nullable|max:5',
        ]);

        $user = User::findOrFail(auth()->user()->id);

        if (isset($v['email_notification'])) {
            if ($user->email == null) {
                return redirect()->back()->with('alert', [
                    'type' => 'danger',
                    'message' => __('You have to update your <strong>Email Address</strong> before activating email notification!')
                ]);
            } else {
                $user->email_notification = $v['email_notification'] == 'on' ? true : false;
            }
        } else {
            $user->email_notification = false;
        }

        if (isset($v['whatsapp_notification'])) {
            if ($user->whatsapp == null) {
                return redirect()->back()->with('alert', [
                    'type' => 'danger',
                    'message' => __('You have to update your <strong>Whatsapp Number</strong> before activating Whatsapp notification')
                ]);
            } else {
                $user->whatsapp_notification = $v['whatsapp_notification'] == 'on' ? true : false;
            }
        } else {
            $user->whatsapp_notification = false;
        }

        $user->save();

        return redirect()->back()->with('alert', [
            'type' => 'success',
            'message' => __('Notification setting has been updated')
        ]);
    }

    public function validateCode(Request $request)
    {
        $v = $request->validate([
            'type' => 'required|max:10',
            'code' => 'required|max:6',
            'email' => 'nullable|max:190',
            'whatsapp' => 'nullable|max:20',
        ]);

        $user = User::findOrFail(auth()->user()->id);

        if ($v['type'] == 'email') {
            if (!isset($v['email'])) {
                return redirect()->back()->with('alert', [
                    'type' => 'danger',
                    'message' => __('Email can\'t be empty')
                ]);
            }

            if ($v['code'] == $user->email_notification_activation) {
                $user->email = $v['email'];
                $user->save();

                return redirect()->back()->with('alert', [
                    'type' => 'success',
                    'message' => __('Email has been changed')
                ]);
            } else {
                return redirect()->back()->with('alert', [
                    'type' => 'danger',
                    'message' => __('Code is not valid')
                ]);
            }
        } else if ($v['type'] == 'whatsapp') {
            if (!isset($v['whatsapp'])) {
                return redirect()->back()->with('alert', [
                    'type' => 'danger',
                    'message' => __('Whatsapp can\'t be empty')
                ]);
            }

            if ($v['code'] == $user->whatsapp_notification_activation) {
                $user->whatsapp = $v['whatsapp'];
                $user->save();

                return redirect()->back()->with('alert', [
                    'type' => 'success',
                    'message' => __('Whatsapp has been changed')
                ]);
            } else {
                return redirect()->back()->with('alert', [
                    'type' => 'danger',
                    'message' => __('Code is not valid')
                ]);
            }
        }
    }
}

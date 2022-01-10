<?php

namespace App;

class Fonnte
{
    public $ch;
    public $type;
    public $phone;
    public $text;
    public $log;

    public function __construct($type = 'text')
    {
        $this->ch = curl_init();
        $this->type = $type;

        $this->log = new Log;
        if (auth()->check()) {
            $this->log->user_id = auth()->user()->id;
        }
    }

    public function to($phone_number)
    {
        $this->phone = $phone_number;

        return $this;
    }

    public function send($message)
    {
        try {
            $data = [
                'phone' => $this->phone,
                'type' => 'text',
                'text' => $message
            ];

            curl_setopt_array($this->ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => [
                    "Authorization: " . config('fronnte.api.key')
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POSTFIELDS => http_build_query($data),
                CURLOPT_URL => 'https://fonnte.com/api/send_message.php',
            ]);

            $result = json_decode(curl_exec($this->ch));

            if ($result->status == true) {
                $this->log->type = 'success';
            } else {
                $this->log->type = 'error';
            }

            $this->log->content = json_encode($result);

            curl_close($this->ch);

            $this->log->save();

            return $result;
        } catch (\Throwable $th) {
            $this->log->type = 'error';
            $this->log->content = 'curl ke REST API Fronnte gagal';

            $this->log->save();

            throw $th;
        }
    }
}

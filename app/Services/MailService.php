<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * 従業員へのメール
     * @param $param
     */
    public function sendMailToVendor($params)
    {
        // メールデータ作成
        $mailData = [];
        foreach ($params as $key => $value) {
            $mailData[$key] = $value;
        };

        Mail::send(
            ['text' => 'emails.reservations.vendor'],
            $mailData,
            function ($message) {
                $message
                    ->to('tatataabcd@gmail.com')
                    ->subject("予約を受け付けました");
            }
        );
    }

    /**
     * 利用者へのメール
     * @param $param
     */
    public function sendMailToUser($params, $viewFile, $subject, $attachFile = null)
    {
        // メールデータ作成
        $mailData = [];
        foreach ($params as $key => $value) {
            $mailData[$key] = $value;
        };

        Mail::send(
            ['text' => $viewFile],
            $mailData,
            function ($message) use ($params, $subject, $attachFile) {
                if (!empty($attachFile)) {
                    $message
                        ->to($params['email'])
                        ->subject($subject)
                        ->attach(storage_path($attachFile));
                } else {
                    $message
                        ->to($params['email'])
                        ->subject($subject);
                }
            }
        );
    }
}

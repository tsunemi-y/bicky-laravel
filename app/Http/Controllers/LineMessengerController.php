<?php

namespace App\Http\Controllers;

use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use App\Models\Reservation;

class LineMessengerController extends Controller
{
    // メッセージ送信用
    public function sendMessage($userId, $message)
    {
        // LINEBOTSDKの設定
        $httpClient = new CurlHTTPClient(config('services.line.channel_token'));
        $bot = new LINEBot($httpClient, ['channelSecret' => config('services.line.messenger_secret')]);

        // メッセージ送信
        $textMessageBuilder = new TextMessageBuilder($message);
        $bot->pushMessage($userId, $textMessageBuilder);
    }

    // 予約一覧リストのメッセージ作成
    public function sendReservationListMessage()
    {
        $userId = config('services.line.admin_id');
        $today = date("Y-m-d H:i:s");
        $reservationModel = new Reservation;
        $todayReservationList = $reservationModel->with('user')->where('reservation_date', '=', $today)->oldest('reservation_time')->get();
        if (count($todayReservationList) == 0) {
            $message = '本日の予約者はいません。';
        } else {
            // 本日の予約者がいる場合、名前と時間をスタッフに送信
            $todayReservationListCount = count($todayReservationList);
            $message = '本日の予約者は下記の通りです。' . "\n" . "\n";
            foreach ($todayReservationList as $key => $rsv) {
                $message .= $rsv->user->parentName . ': ' . $rsv->reservation_time;

                if ($todayReservationListCount != $key + 1) {
                    $message .= "\n";
                }
            }
        }

        $this->sendMessage($userId, $message);
    }

    // 予約があった場合にメッセージ送信
    public function sendReservationMessage($name, $name2, $reservationDate, $reservationTime)
    {
        $userId = config('services.line.admin_id');

        $message = 'ご予約を受け付けました。' . "\n" . "\n";
        $message .= "利用児氏名：　{$name}" . "\n";
        if (!empty($name2)) $message .= "利用児2氏名：　{$name2}" . "\n";
        $message .= "予約日時：　{$reservationDate}" . "\n";
        $message .= "予約時間：　{$reservationTime}";

        $this->sendMessage($userId, $message);
    }

    // 予約キャンセルがあった場合にメッセージ送信
    public function sendCancelReservationMessage($name, $name2, $reservationDate, $reservationTime)
    {
        $userId = config('services.line.admin_id');

        $message = 'ご予約がキャンセルされました。' . "\n" . "\n";
        $message .= "利用児氏名：　{$name}" . "\n";
        if (!empty($name2)) $message .= "利用児2氏名：　{$name2}" . "\n";
        $message .= "予約日時：　{$reservationDate}" . "\n";
        $message .= "予約時間：　{$reservationTime}";

        $this->sendMessage($userId, $message);
    }

    // 新規登録者のメッセージ作成
    public function sendRegistrationMessage($user)
    {
        $userId = config('services.line.admin_id');
        $coursePlan = convertCourseFeeToName($user->fee);

        $message = '新規登録を受付ました。' . "\n" . "\n";
        $message .= "保護者氏名：　$user->parentName" . "\n";
        $message .= "メールアドレス：　$user->email" . "\n";
        $message .= "電話番号：　$user->tel" . "\n";
        $message .= "利用児氏名：　$user->childName" . "\n";
        $message .= "年齢：　$user->age" . "\n";
        $message .= "性別：　$user->gender" . "\n";
        if (!empty($user->diagnosis)) $message .= "診断名：　$user->diagnosis" . "\n";
        if (!empty($user->childName2)) $message .= "利用児氏名2：　$user->childName2" . "\n";
        if (!empty($user->age2)) $message .= "年齢2：　$user->age2" . "\n";
        if (!empty($user->gender2)) $message .= "性別2：　$user->gender2" . "\n";
        if (!empty($user->diagnosis2)) $message .= "診断名2：　$user->diagnosis2" . "\n";
        $message .= "住所：　$user->address" . "\n";
        if (!empty($user->introduction)) $message .= "紹介先：　$user->introduction" . "\n";
        if (!empty($user->consaltation)) $message .= "相談内容：　$user->consaltation" . "\n";
        $message .= "ご利用プラン：　{$coursePlan}" . "\n";
        $message .= "料金：　$user->fee";
        
        $this->sendMessage($userId, $message);
    }
}

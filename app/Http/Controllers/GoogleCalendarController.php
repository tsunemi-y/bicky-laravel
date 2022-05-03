<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendarController extends Controller
{
    public function store($parentName, $startDateTime, $endDateTime)
    {
        try {
            $client = $this->getClient();
            $service = new Google_Service_Calendar($client);
            $calendarId = config('services.google_calendar.id');

            $event = new Google_Service_Calendar_Event([
                //タイトル
                'summary' => $parentName,
                'start' => array(
                    // 開始日時
                    'dateTime' => str_replace(' ', 'T', date('Y-m-d H:i:sP', strtotime($startDateTime))),
                    'timeZone' => 'Asia/Tokyo',
                ),
                'end' => array(
                    // 終了日時
                    'dateTime' => str_replace(' ', 'T', date('Y-m-d H:i:sP', strtotime($endDateTime))),
                    'timeZone' => 'Asia/Tokyo',
                ),
            ]);

            $event = $service->events->insert($calendarId, $event);
        } catch (\Exception $e) {
            \Log::info($e);
        }
    }

    private function getClient()
    {
        $client = new Google_Client();

        //アプリケーション名
        $client->setApplicationName('ビッキー_予約者一覧');
        //権限の指定
        $client->setScopes(Google_Service_Calendar::CALENDAR_EVENTS);
        //JSONファイルの指定
        $client->setAuthConfig(storage_path('bicky-347713-47d82b536dcd.json'));

        return $client;
    }
}

<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use App\Models\GoogleCaleandar;
use Google_Service_Calendar_Event;

class GoogleCalendarService
{
    private $client;
    private $service;
    private $calendarId;

    public function __construct()
    {
        $this->client = $this->getClient();
        $this->service = new Google_Service_Calendar($this->client);
        $this->calendarId = config('services.google_calendar.id');
    }

    public function store($parentName, $startDateTime, $endDateTime, $reservationId)
    {
        try {
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

            $event = $this->service->events->insert($this->calendarId, $event);
            $seachStr = '=';
            $eventId = mb_substr($event->htmlLink, mb_strrpos($event->htmlLink, $seachStr) + 1, mb_strlen($event->htmlLink));
            
            // イベントIDをDBに登録
            $googleCalendar = new GoogleCaleandar();
            $googleCalendar->create([
                'reservation_id' => $reservationId,
                'event_id' => $eventId,
            ]);
        } catch (\Exception $e) {
            \Log::info($e);
        }
    }

    public function delete($reservationId)
    {
        try {
            $googleCalendar = new GoogleCaleandar();
            $googleCalendar = $googleCalendar->where('reservation_id', $reservationId)->first();

            $eventId = explode(' ', base64_decode($googleCalendar->event_id))[0];

            $this->service->events->delete($this->calendarId, $eventId);

            $googleCalendar->delete($reservationId);
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
        $client->setAuthConfig(storage_path('app/api-key/bicky-347713-47d82b536dcd.json'));

        return $client;
    }
}

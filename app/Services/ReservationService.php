<?php

namespace App\Services;

use \Yasumi\Yasumi;
use App\Models\User;
use App\Models\Reservation;
use App\Services\MailService;
use App\Consts\ConstReservation;
use App\Services\LineMessengerServices;
use App\Repositories\ReservationRepository;
use App\Models\AvailableReservationDatetime;
use App\Repositories\AvailableReservationDatetimeRepository;

class ReservationService
{
    public function __construct(
        private GoogleCalendarService $googleCalendarService, 
        private AvailableReservationDatetimeService $availableReservationDatetimeService,
        private ReservationRepository $reservationRepository,
        private AvailableReservationDatetimeRepository $availableReservationDatetimeRepository
    ) {
    }

    // todo:html部分をビューに移行。ビューの日付押下時のエラー修正
    public function createCalender($request)
    {
        $avaDatetimes = $this->getMappingAvailableDatesAndTimes();
        $avaDates = $avaDatetimes['avaDates'];
        $avaTimes = $avaDatetimes['avaTimes'];

        // カレンダーに必要な情報取得
        if (!empty($request->ym)) {
            $targetYearMonth = $request->ym;
        } else {
            $targetYearMonth = date('Y-m');
        }

        // カレンダーに必要な情報取得
        $timestamp = strtotime($targetYearMonth . '-01');
        $today = date('Y-m-j');
        $calenderTitle = date('Y年n月', $timestamp);
        $dayCount = date('t', $timestamp);
        $week = date('w', $timestamp);
        $saturdayNum = 6;
        $sundayNum = 0;
        $dayAfterTomorrow = date('Y-m-d', strtotime('+2 day'));
        $prevMonth = date('Y-m', strtotime('-1 month', $timestamp));
        $nextMonth = date('Y-m', strtotime('+1 month', $timestamp));

        // 祝日取得
        $holidays = Yasumi::create('Japan', date('Y'), 'ja_JP');

        // カレンダーの中身を格納する変数
        $calenders = [];
        $calender = '';

        // 初日と曜日の位置調整
        $calender .= str_repeat('<td></td>', $week);

        for ($day = 1; $day <= $dayCount; $day++, $week++) {
            /*============================
                    日付処理　ここから
            ============================*/
            $day = str_pad($day, 2, '0', STR_PAD_LEFT); //　一桁の日の場合、０詰め
            $displayedDate = $targetYearMonth . '-' . $day;

            $isAvailableDate = false;
            if (in_array($displayedDate, $avaDates, true)) $isAvailableDate = true;

            // 条件に応じてクラス付与
            $calender .= '<td ';
            if ($today == $displayedDate) $calender .= 'class="today" ';
            if ($holidays->isHoliday(new \DateTime($displayedDate)) || $week == 0) $calender .= 'class="holiday" ';
            if ($week == $saturdayNum) $calender .= 'class="saturday" ';
            $calender .= '>' . $day;
            if (strtotime($displayedDate) <= strtotime($dayAfterTomorrow)) {
                $calender .= "<p class='hyphen'>-</p>";
            } else if ($isAvailableDate && !$holidays->isHoliday(new \DateTime($displayedDate))) {
                $calender .= "<p class='circle day-ok' data-date='$displayedDate'>○</p>";
            } else {
                $calender .= "<p class='cross'>×</p>";
            }

            $calender .= '</td>';
            //月の最終日の後に空セルを追加
            if ($day == $dayCount) $calender .= str_repeat('<td></td>', $saturdayNum - $week);

            //週・月終わりの場合、改行
            if ($week == $saturdayNum || $day == $dayCount) {
                $calenders[] = '<tr>' . $calender . '</tr>';
                $calender = '';
            }
            /*============================
                    日付処理　ここまで
            ============================*/

            if ($week == $saturdayNum) $week = -1; // 土曜日の次は改行
        }
        return compact('calenders', 'calenderTitle', 'prevMonth', 'nextMonth', 'avaTimes');
    }

    public function createReservation(Reservation $reservation, $request, $userId, $endTime)
    {
        return $reservation->create([
            'user_id' => $userId,
            'reservation_date' => $request->avaDate,
            'reservation_time' => $request->avaTime,
            'end_time' => $endTime,
        ]);
    }

    public function existsDuplicateReservation($request)
    {
        $reservedDateTime = Reservation::query()
            ->where('reservation_date', $request->avaDate)
            ->where('reservation_time', $request->avaTime)
            ->first();

        return !empty($reservedDateTime);
    }

    public function calculateReservationEndTime($request, $userId)
    {
        $useTime = User::find($userId)->use_time;

        return date('H:i:s', strtotime("{$request->avaTime} +{$useTime} minute -1 second"));
    }

    public function getMappingAvailableDatesAndTimes()
    {
        $avaDatetimes = $this->reservationRepository->getAvailableDatetimes();

        $avaDates = [];
        $avaTimes = [];
        foreach ($avaDatetimes as $datetime) {
            $avaDates[] = $datetime->available_date;

            $tmpAvaTimes = toArrayFromArrayColumn($datetime->available_times);
            $avaTimes[$datetime->available_date] = $tmpAvaTimes;
        }

        return compact('avaDates', 'avaTimes');
    }

    public function getReservations()
    {
        $tmpReservations = $this->reservationRepository->getReservations();

        $reservations = [];
        foreach ($tmpReservations as $tr) {
            $reservations[$tr->reservation_date][] = [
                'reservationName' => $tr->parentName,
                'reservationTime' => $tr->reservation_time
            ];
        }

        return $reservations;
    }

    public function getHolidays()
    {
        $holidays = Yasumi::create('Japan', date('Y'), 'ja_JP');
        return array_values($holidays->getHolidayDates());
    }

    public function saveAvailableDatetime($request)
    {
        $datetime = $request['datetime'];
        $date = substr($datetime, 0, 10);
        $time = substr($datetime, 11, 15);
        $monthCount = date('t', strtotime($date));
        $nonDayDate = substr($date, 0, 8); // 例）2022/05/

        if ($request['isBulkWeekend']) {
            $targetInsertWeeks = [6, 0];
            $this->bulkInsert($monthCount, $nonDayDate, $targetInsertWeeks);
        } elseif ($request['isBulkMonth']) {
            $targetInsertWeeks = [0, 1, 2, 3, 4, 5, 6];
            $this->bulkInsert($monthCount, $nonDayDate, $targetInsertWeeks);
        } elseif ($request['isBulkDay']) {
            $insertDatetimes = $this->availableReservationDatetimeService->getInsertDatetimes($date);
            $this->availableReservationDatetimeRepository->bulkInsert($insertDatetimes);
        } else {
            $avaRsvDatetimeModel = new AvailableReservationDatetime();
            $avaRsvDatetimeModel->create([
                'available_date' => $date,
                'available_time' => $time,
            ]);
        }
    }

    private function bulkInsert($monthCount, $nonDayDate, $targetInsertWeeks)
    {
        $holidays = Yasumi::create('Japan', date('Y'), 'ja_JP');

        $insertDatetimes = [];
        for ($i = 1; $i <= $monthCount; $i++) {
            $week = (int)date('w', strtotime($nonDayDate. $i));
            if (!(in_array($week, $targetInsertWeeks, true))) continue;
            if ($holidays->isHoliday(new \DateTime($nonDayDate . $i))) continue;
            foreach (ConstReservation::AVAILABLE_TIME_LIST as $time) {
                $insertDatetimes[] = [
                    'available_date' => $nonDayDate. $i,
                    'available_time' => $time,
                ];
            }
        }
        
        $this->availableReservationDatetimeRepository->bulkInsert($insertDatetimes);
    }
}

<?php

namespace App\Http\Controllers;

use \Yasumi\Yasumi;
use App\Models\User;
use App\Models\Reservation;
use App\Services\MailService;
use App\Traits\Reservationable;

use App\Services\ReservationService;
use App\Services\GoogleCalendarService;
use App\Services\LineMessengerServices;
use App\Http\Requests\ReservationFormRequest;
use App\Http\Requests\ReservationCalenderFormRequest;

class ReservationController extends Controller
{
    use Reservationable;

    public function __construct(private ReservationService $reservationService, private GoogleCalendarService $googleCalendarService)
    {
    }

    public function index(ReservationCalenderFormRequest $request)
    {
        $calenderInfo = $this->createCalender($request);
        return view('pages.reservations.reservation', compact('calenderInfo'));
    }

    public function createCalender($request)
    {

        // 予約可能日時取得
        $tmpAvaDatetimes = $this->getTmpAvailableReservationDatetimes();

        // 予約されている日時取得
        $reserveDateTimes = $this->getReservationDatetimes();

        $avaDatetimes = $this->getAvailableReservationDatetimes($tmpAvaDatetimes, $reserveDateTimes);

        $avaDates = $avaDatetimes['avaDates'];
        $avaTimes = $avaDatetimes['avaDatetimes'];

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
        $nowDate = date('Y-m-d');
        $prevMonth = date('Y-m', strtotime('-1 month', $timestamp));
        $nextMonth = date('Y-m', strtotime('+1 month', $timestamp));

        // 祝日取得
        $holidays = Yasumi::create('Japan', date('Y'), 'ja_JP');

        // カレンダーの中身を格納する変数
        $calenders = [];
        $calender = '';

        $timeList = [];
        $time = '';

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
            if (strtotime($displayedDate) <= strtotime($nowDate)) {
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

    public function store(ReservationFormRequest $request, Reservation $reservation)
    {
        // 指定した日時がすでに埋まっている場合、予約トップにリダイレクト
        $isReserved = $this->reservationService->existsDuplicateReservation($request);
        if ($isReserved) {
            return redirect(route('reservationTop'))
                ->with('failedReservation', '選択された日時はすでにご予約がございます。</br>違う日時でご予約ください。');
        } 

        $userId = \Auth::id();

        $endTime = $this->reservationService->calculateReservationEndTime($request, $userId);

        $reservedInfo = $this->reservationService->createReservation($reservation, $request, $userId, $endTime);

        $userInfo = User::find($userId);

        $this->reservationService->sendReservationMessage($request, $userInfo, $reservedInfo);

        $this->googleCalendarService->store($userInfo->parentName, $request->avaDate. $request->avaTime, $request->avaDate. $endTime, $reservedInfo->id);

        return redirect(route('reservationTop'))
            ->with('successReservation', '予約を受け付けました。</br>予約内容確認のメールをお送りしました。');
    }

    /**
     * 予約キャンセルメッセージ送信
     * @param $param
     * @return void
     */
    public function sendCancelReservationMessage($params)
    {
        // 日時のフォーマット変更
        $params['reservationTime'] = formatTime($params['reservationTime']);
        $params['reservationDate'] = formatDate($params['reservationDate']);

        // 管理者へLINEメッセージ送信
        $lineMessenger = new LineMessengerServices();
        $lineMessenger->sendCancelReservationMessage($params['childName'], $params['childName2'], $params['reservationDate'], $params['reservationTime']);

        // 利用者へのメールに必要なデータ設定
        $mailService = new MailService();
        $viewFile = 'emails.reservations.cancel';
        $subject = '予約をキャンセルしました';
        $mailService->sendMailToUser($params, $viewFile, $subject);
    }

    /**
     * 予約キャンセル画面表示
     * @return void
     */
    public function dispReservationCancel(Reservation $reservation)
    {
        return view('pages.reservations.reservationCancel', compact('reservation'));
    }

    /**
     * 予約キャンセル
     * @param App\Models\Reservation
     * @return void
     */
    public function cancelReservation(Reservation $reservation)
    {
        $reservation->delete();

        $messageData = [
            'reservationDate' => $reservation->reservation_date,
            'reservationTime' => $reservation->reservation_time,
            'childName'       => $reservation->user->childName,
            'childName2'      => $reservation->user->childName2,
            'email'           => $reservation->user->email,
        ];

        $this->sendCancelReservationMessage($messageData);

        $googleCalendar = new GoogleCalendarService();
        $googleCalendar->delete($reservation->id);

        return redirect(route('reservationTop'))->with('reservationCancel', '予約をキャンセルしました。');
    }
}

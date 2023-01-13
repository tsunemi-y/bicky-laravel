<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;

use App\Services\MailService;
use App\Services\ReservationService;
use App\Services\GoogleCalendarService;
use App\Services\LineMessengerServices;
use App\Http\Requests\ReservationFormRequest;
use App\Http\Requests\ReservationCalenderFormRequest;

class ReservationController extends Controller
{
    public function __construct(
        private ReservationService $reservationService, 
        private GoogleCalendarService $googleCalendarService,
        private MailService $mailService, 
        private LineMessengerServices $lineMessengerServices,
    )
    {
    }

    public function index(ReservationCalenderFormRequest $request)
    {
        $calenderInfo = $this->reservationService->createCalender($request);
        return view('pages.reservations.reservation', compact('calenderInfo'));
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

        $messageData = [
            'childName' => $userInfo->childName,
            'childName2' => $userInfo->childName2,
            'reservationDate' => formatDate($request->avaDate),
            'reservationTime' => formatTime($request->avaTime),
            'email' => $userInfo->email,
            'reservationId' => $reservedInfo->id,
        ];

        // 管理者へ予約通知のLINEメッセージ送信
        $this->lineMessengerServices->sendReservationMessage($messageData['childName'], $messageData['childName2'], $messageData['reservationDate'], $messageData['reservationTime']);

        $viewFile = 'emails.reservations.user';
        $subject = '予約を受け付けました';
        $this->mailService->sendMailToUser($messageData, $viewFile, $subject);

        $this->googleCalendarService->store($userInfo->parentName, $request->avaDate. $request->avaTime, $request->avaDate. $endTime, $reservedInfo->id);

        return redirect(route('reservationTop'))
            ->with('successReservation', '予約を受け付けました。</br>予約内容確認のメールをお送りしました。');
    }

    public function show(Reservation $reservation)
    {
        return view('pages.reservations.reservationCancel', compact('reservation'));
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        $messageData = [
            'reservationDate' => formatDate($reservation->reservation_date),
            'reservationTime' => formatTime($reservation->reservation_time),
            'childName'       => $reservation->user->childName,
            'childName2'      => $reservation->user->childName2,
            'email'           => $reservation->user->email,
        ];

        // 管理者へ予約キャンセルのLINEメッセージ送信
        $this->lineMessengerServices->sendCancelReservationMessage($messageData['childName'], $messageData['childName2'], $messageData['reservationDate'], $messageData['reservationTime']);

        $viewFile = 'emails.reservations.cancel';
        $subject = '予約をキャンセルしました';
        $this->mailService->sendMailToUser($messageData, $viewFile, $subject);

        $this->googleCalendarService->delete($reservation->id);

        return redirect(route('reservationTop'))->with('reservationCancel', '予約をキャンセルしました。');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;

use App\Services\ReservationService;
use App\Services\GoogleCalendarService;
use App\Http\Requests\ReservationFormRequest;
use App\Http\Requests\ReservationCalenderFormRequest;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservationService, private GoogleCalendarService $googleCalendarService)
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

        $this->reservationService->sendReservationMessage($request, $userInfo, $reservedInfo);

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

        $this->reservationService->sendCancelReservationMessage($reservation);

        $this->googleCalendarService->delete($reservation->id);

        return redirect(route('reservationTop'))->with('reservationCancel', '予約をキャンセルしました。');
    }
}

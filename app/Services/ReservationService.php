<?php

namespace App\Services;

use App\Models\User;
use App\Models\Reservation;
use App\Services\MailService;
use App\Services\LineMessengerServices;

class ReservationService
{

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
        $reservationModel = new Reservation;

        // 同じ日時に予約があるときはエラー
        $reservedDateTime = $reservationModel
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

    public function sendReservationMessage($request, $userInfo, $reservedInfo)
    {
        // メール文面作成用のパラメータ作成
        $mailData = [
            'childName' => $userInfo->childName,
            'childName2' => $userInfo->childName2,
            'reservationDate' => formatDate($request->avaDate),
            'reservationTime' => formatTime($request->avaTime),
            'email' => $userInfo->email,
            'reservationId' => $reservedInfo->id,
        ];

        // 管理者へLINEメッセージ送信
        $lineMessenger = new LineMessengerServices();
        $lineMessenger->sendReservationMessage($mailData['childName'], $mailData['childName2'], $mailData['reservationDate'], $mailData['reservationTime']);

        // 利用者へのメールに必要なデータ設定
        $mailService = new MailService();
        $viewFile = 'emails.reservations.user';
        $subject = '予約を受け付けました';
        $mailService->sendMailToUser($mailData, $viewFile, $subject);
    }
}

<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Reservation;
use App\Models\AvailableReservationDatetime;

trait Reservationable
{
    /**
     * 予約可能日時テーブルから予約可能日時取得
     *
     * @return Array
     */
    public function getTmpAvailableReservationDatetimes()
    {
        $avaReserveDatetimes = AvailableReservationDatetime::orderBy('available_date', 'asc')->orderBy('available_time', 'asc')->get(['id', 'available_date', 'available_time']);
        $avaTimes = [];
        foreach ($avaReserveDatetimes as $avaReserveDateTime) {
            $avaTimes[$avaReserveDateTime->available_date][] = $avaReserveDateTime->available_time;
        }
        return $avaTimes;
    }

    /**
     * 予約テーブルから予約日時取得
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getReservationDatetimes()
    {
        $reservations = Reservation::all();
        $reserveDateTimes = [];
        $i = 0;
        foreach ($reservations as $reservation) {
            $reserveDateTimes[$reservation->reservation_date][$i]['start_time'] = $reservation->reservation_time;
            $reserveDateTimes[$reservation->reservation_date][$i]['end_time'] = $reservation->end_time;
            $i++;
        }
        return $reserveDateTimes;
    }

    /**
     * 利用可能時間と予約時間から画面に表示される利用可能時間を取得
     * @return Array
     */
    public function getAvailableReservationDatetimes($tmpAvaDatetimes, $reserveDateTimes)
    {
        $avaDates = array_keys($tmpAvaDatetimes);
        $avaDatetimes = [];

        $userId = \Auth::id();
        $useTime = User::find($userId)->use_time;

        foreach ($tmpAvaDatetimes as  $avaDate => $avaTimes) {
            foreach ($reserveDateTimes as $rsvDate => $rsvTimes) {
                if ($avaDate !== $rsvDate) continue;
                foreach ($rsvTimes as $rsvTime) {
                    foreach ($avaTimes as $avaTime) {
                        // 予約start~toの間の予約可能日時削除
                        if (!((date('H:i:s', strtotime("{$avaTime} +{$useTime} minute -1 second")) < $rsvTime['start_time']) || $rsvTime['end_time'] < $avaTime)) {
                            $deleteTimeKey = array_search($avaTime, $tmpAvaDatetimes[$avaDate]);
                            unset($tmpAvaDatetimes[$avaDate][$deleteTimeKey]);
                        }
                    }
                }
            }

            $avaDatetimes[$avaDate] = $tmpAvaDatetimes[$avaDate];
            $avaDatetimes[$avaDate] = array_values($avaDatetimes[$avaDate]); // 配列順序並び替え

            // 時間形式をhh時ss秒に変更
            foreach ($avaDatetimes[$avaDate] as $key => $time) {
                $formattedTime = formatTime($time);
                $avaDatetimes[$avaDate][$key] = $formattedTime; // フォーマット前時間をフォーマット後時間で上書き
            }

            // 時間を持っていない日付を削除
            if (empty($tmpAvaDatetimes[$avaDate])) {
                $deleteDateKey = array_search($avaDate, $avaDates);
                unset($avaDates[$deleteDateKey]);
            }
        }

        return compact('avaDatetimes', 'avaDates');
    }
}

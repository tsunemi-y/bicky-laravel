<?php

namespace App\Services;

use App\Consts\ConstReservation;

class AvailableReservationDatetimeService
{
    public function getInsertDatetimes($date)
    {
        $insertDatetimes = [];
        foreach (ConstReservation::AVAILABLE_TIME_LIST as $time) {
            $insertDatetimes[] = [
                'available_date' => $date,
                'available_time' => $time,
            ];
        }
        return $insertDatetimes;
    }  
}

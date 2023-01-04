<?php

namespace App\Repositories;

use DB;
use Illuminate\Support\Collection;
use App\Models\AvailableReservationDatetime;

class ReservationRepository
{
    public function getAvailableDatetimes(): Collection
    {
        return AvailableReservationDatetime::query()
            ->select(DB::raw('
            available_date
            ,array_agg(available_time) available_times
        '))
            ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                ->from('reservations')
                ->whereRaw('CAST(available_date AS DATE) = CAST(reservation_date AS DATE)')
                ->whereRaw('CAST(available_time AS TIME) BETWEEN CAST(reservation_time AS TIME) AND CAST(end_time AS TIME)');
            })
            ->groupBy('available_date')
            ->get();
    }
}
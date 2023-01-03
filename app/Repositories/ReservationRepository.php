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
            cast(available_date)
            ,array_agg(available_time) available_times
        '))
            ->whereNotExists(function($query) {
            $query->select(DB::raw(1))
                ->from('reservations')
                ->where('reservation_date', 'available_date')
                ->whereBetween('available_time', ['reservation_time', 'end_time']);
        })
            ->groupBy('available_date')
            ->get();
    }
}
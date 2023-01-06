<?php

namespace App\Repositories;

use DB;

class AvailableReservationDatetimeRepository
{
    public function bulkInsert($insertDatetimes)
    {
        DB::table('available_reservation_datetimes')->insert($insertDatetimes);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AvailableReservationDatetime;

class AvailableReservationDatetimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timeList = [
            [
                'available_time' => '10:00',
                'available_date' => '2023/1/14'
            ],
            [
                'available_time' => '11:00',
                'available_date' => '2023/1/14'
            ],
            [
                'available_time' => '13:00',
                'available_date' => '2023/1/14'
            ],
            [
                'available_time' => '14:00',
                'available_date' => '2023/1/15'
            ],
        ];

        foreach ($timeList as $time) {
            AvailableReservationDatetime::create([
                'available_time' => $time['available_time'],
                'available_date' => $time['available_date'],
            ]);
        }
    }
}

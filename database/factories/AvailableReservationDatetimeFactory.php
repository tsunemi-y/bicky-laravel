<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AvailableReservationDatetimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'available_date' => '2100/01/01',
            'available_time' => '11:00:00',
            'fee_id' => 1200
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->randomDigit,
            'reservation_date' => $this->faker->date,
            'reservation_time' => date('H:i' ,strtotime($this->faker->time)),
            'end_time' => date('H:i' ,strtotime($this->faker->time)),
        ];
    }
}

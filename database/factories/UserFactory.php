<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'parentName' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'tel' => 555,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'childName' => $this->faker->name(),
            'age' => 5,
            'gender' => 'men',
            'diagnosis' => 'ADHD',
            'childName2' => $this->faker->name(),
            'age2' => 5,
            'gender2' => 'women',
            'diagnosis2' => 'ASD',
            'address' => 'osaka',
            'introduction' => '役場',
            'coursePlan' => 1,
            'consaltation' => null,
            'fee' => 13200,
            'userAgent' => null,
            'use_time' => 120,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}

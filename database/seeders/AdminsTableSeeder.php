<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('admins')->insert([
            'name'              => 'admin',
            'email'             => 'test@gmail.com',
            'password'          => \Hash::make('testpass'),
            'remember_token'    => \Str::random(10),
        ]);
    }
}

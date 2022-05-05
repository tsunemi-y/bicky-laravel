<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use App\Http\Controllers\GoogleCalendarController;

class googleCalendarStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'googlecalendar:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '予約者をgoogleカレンダーに登録';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $googleCalendar = new GoogleCalendarController();
        $reservation = new Reservation();
        $reservations = $reservation->with('user')->get();
        foreach ($reservations as $reservation) {
            $googleCalendar->store($reservation->user->parentName, $reservation->reservation_date . $reservation->reservation_time, $reservation->reservation_date . $reservation->end_time);
        }
    }
}

<?php

namespace App\Services;

use \Yasumi\Yasumi;
use App\Models\User;
use App\Consts\ConstUser;
use App\Models\Reservation;
use App\Services\MailService;
use App\Consts\ConstReservation;
use App\Services\LineMessengerServices;
use App\Repositories\ReservationRepository;
use App\Models\AvailableReservationDatetime;
use App\Repositories\AvailableReservationDatetimeRepository;

class UserService
{
    public function __construct(
        private GoogleCalendarService $googleCalendarService, 
        private MailService $mailService, 
        private LineMessengerServices $lineMessengerServices,
        private AvailableReservationDatetimeService $availableReservationDatetimeService,
        private ReservationRepository $reservationRepository,
        private AvailableReservationDatetimeRepository $availableReservationDatetimeRepository
    ) {
    }

    public function getUseTimeByFee($fee)
    { 
        if ($fee === ConstUser::FEE_ONE_SIBLING || $fee === ConstUser::FEE_TWO_SIBLING) {
            return ConstUser::LONG_USE_TIME;
        } else {
            return ConstUser::NORMAL_USE_TIME;
        }
    }

    public function putPDF($fee)
    { 
        if ($fee === ConstUser::FEE_ONE_SIBLING || $fee === ConstUser::FEE_TWO_SIBLING) {
            return ConstUser::LONG_USE_TIME;
        } else {
            return ConstUser::NORMAL_USE_TIME;
        }
    }
}

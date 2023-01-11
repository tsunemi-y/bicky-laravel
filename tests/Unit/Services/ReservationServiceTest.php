<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Reservation;
use App\Services\MailService;
use App\Services\ReservationService;
use App\Services\GoogleCalendarService;
use App\Services\LineMessengerServices;
use App\Repositories\ReservationRepository;
use App\Services\AvailableReservationDatetimeService;
use App\Repositories\AvailableReservationDatetimeRepository;

class ReservationServiceTest extends TestCase
{
    private ReservationService $reservationService;
    private GoogleCalendarService $googleCalendarService;
    private MailService $mailService; 
    private LineMessengerServices $lineMessengerServices;
    private AvailableReservationDatetimeService $availableReservationDatetimeService;
    private ReservationRepository $reservationRepository;
    private AvailableReservationDatetimeRepository $availableReservationDatetimeRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->googleCalendarService = new GoogleCalendarService();
        $this->mailService = new MailService();
        $this->lineMessengerServices = new LineMessengerServices();
        $this->availableReservationDatetimeService = new AvailableReservationDatetimeService();
        $this->reservationRepository = new ReservationRepository();
        $this->availableReservationDatetimeRepository = new AvailableReservationDatetimeRepository();

        $this->reservationService = new ReservationService(
            $this->googleCalendarService,
            $this->mailService,
            $this->lineMessengerServices,
            $this->availableReservationDatetimeService,
            $this->reservationRepository,
            $this->availableReservationDatetimeRepository
        );
    }

    public function testCreateReservation(): void
    {
        $createCount = 1;
        $reservations = Reservation::factory($createCount)->create()->toArray();
        self::assertCount($createCount, $reservations);
    }

    public function testExistsDuplicateReservation(): void
    {
        $date = '2023/1/1';
        $time = '11:00:00';

        Reservation::factory(2)->create([
            'reservation_date' => $date,
            'reservation_time' => $time, 
        ]);

        $isDuplicate = $this->reservationService->existsDuplicateReservation((object) [
            'avaDate' => $date,
            'avaTime' => $time,
        ]);

        self::assertTrue($isDuplicate);
    }
}
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Reservation;
use App\Services\MailService;
use App\Services\ReservationService;
use App\Services\GoogleCalendarService;
use App\Services\LineMessengerServices;
use App\Repositories\ReservationRepository;
use App\Models\AvailableReservationDatetime;
use App\Services\AvailableReservationDatetimeService;
use App\Repositories\AvailableReservationDatetimeRepository;

class ReservationServiceTest extends TestCase
{
    private ReservationService $reservationService;
    private GoogleCalendarService $googleCalendarService;
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

    public function testCalculateReservationEndTime(): void
    {
        $time = '11:00:00';

        $userFactory = User::factory(1)->create()[0];
        $user = User::query()->where('parentName', $userFactory->parentName)->get()[0];
        
        $endTime = $this->reservationService->calculateReservationEndTime((object) [
            'avaTime' => $time,
        ], $user->id);

        self::assertTrue($endTime == date('H:i:s', strtotime("{$time} +{$user->use_time} minute -1 second")));
    }

    public function testGetMappingAvailableDatesAndTimes(): void
    {
        AvailableReservationDatetime::factory(1)->create();

        $avaDatetimes = $this->reservationRepository->getAvailableDatetimes();

        $avaDates = [];
        $avaTimes = [];
        foreach ($avaDatetimes as $datetime) {
            $avaDates[] = $datetime->available_date;

            $tmpAvaTimes = toArrayFromArrayColumn($datetime->available_times);
            $avaTimes[$datetime->available_date] = $tmpAvaTimes;
        }
        $avaDatetimes = [
            'avaDates' => $avaDates,
            'avaTimes' => $avaTimes,
        ];

        $tartgetAvaDatetimes = $this->reservationService->getMappingAvailableDatesAndTimes();

        self::assertSame($avaDatetimes, $tartgetAvaDatetimes);

    }
    
}
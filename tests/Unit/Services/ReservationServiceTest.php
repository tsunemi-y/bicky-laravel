<?php

namespace Tests\Unit\Services;

use Yasumi\Yasumi;
use Tests\TestCase;
use App\Models\User;
use App\Models\Reservation;
use App\Services\MailService;
use App\Consts\ConstReservation;
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

    public function testGetReservations(): void
    {
        $userFactory = User::factory(1)->create()[0];
        $user = User::query()->where('parentName', $userFactory->parentName)->get()[0];

        Reservation::factory(1)->create([
            'user_id' => $user->id,
        ]);

        $tmpReservations = $this->reservationRepository->getReservations();
        $reservations = [];
        foreach ($tmpReservations as $tr) {
            $reservations[$tr->reservation_date][] = [
                'reservationName' => $tr->parentName,
                'reservationTime' => $tr->reservation_time
            ];
        }

        $tartgetReservations = $this->reservationService->getReservations();

        self::assertSame($reservations, $tartgetReservations);
    }
    
    public function testGetHolidays(): void
    {
        $tmpHolidays = Yasumi::create('Japan', date('Y'), 'ja_JP');
        $holidays = array_values($tmpHolidays->getHolidayDates());

        $targetHolidays = $this->reservationService->getHolidays();

        self::assertSame($holidays, $targetHolidays);
    }

    public function testSaveAvailableDatetime(): void
    {
        $datetime = '2033/1/1 10:00:00';
        $date = substr($datetime, 0, 10);
        $time = substr($datetime, 11, 15);
        $monthCount = date('t', strtotime($date));
        $nonDayDate = substr($date, 0, 8); // 例）2022/05/

        // ============週末チェック============
        $this->reservationService->saveAvailableDatetime([
            'datetime' => $datetime,
            'isBulkWeekend' => 1,
            'isBulkMonth' => 0,
            'isBulkDay' => 0,
        ]);

        $targetInsertWeeks = [6, 0];
        $weekEndDatetimes = $this->getInsertDatetimes($monthCount, $nonDayDate, $targetInsertWeeks);

        foreach ($weekEndDatetimes as $weekEndDatetime) {
            $this->assertDatabaseHas('available_reservation_datetimes', [
                'available_date' => $weekEndDatetime['available_date'],
                'available_time' => $weekEndDatetime['available_time'],
            ]);
        }

        // ============一ヶ月============
        $this->reservationService->saveAvailableDatetime([
            'datetime' => $datetime,
            'isBulkWeekend' => 1,
            'isBulkMonth' => 0,
            'isBulkDay' => 0,
        ]);

        $targetInsertWeeks = [6, 0];
        $weekEndDatetimes = $this->getInsertDatetimes($monthCount, $nonDayDate, $targetInsertWeeks);

        foreach ($weekEndDatetimes as $weekEndDatetime) {
            $this->assertDatabaseHas('available_reservation_datetimes', [
                'available_date' => $weekEndDatetime['available_date'],
                'available_time' => $weekEndDatetime['available_time'],
            ]);
        }
    }

    private function getInsertDatetimes($monthCount, $nonDayDate, $targetInsertWeeks)
    {
        $holidays = Yasumi::create('Japan', date('Y'), 'ja_JP');

        $insertDatetimes = [];
        for ($i = 1; $i <= $monthCount; $i++) {
            $week = (int)date('w', strtotime($nonDayDate. $i));
            if (!(in_array($week, $targetInsertWeeks, true))) continue;
            if ($holidays->isHoliday(new \DateTime($nonDayDate . $i))) continue;
            foreach (ConstReservation::AVAILABLE_TIME_LIST as $time) {
                $insertDatetimes[] = [
                    'available_date' => $nonDayDate. $i,
                    'available_time' => $time,
                ];
            }
        }
        return $insertDatetimes;
    }
}
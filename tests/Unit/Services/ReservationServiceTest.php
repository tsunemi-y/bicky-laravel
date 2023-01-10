<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Reservation;
use App\Services\ReservationService;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class ReservationServiceTest extends TestCase
{
    private ReservationService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new ReservationService();
    }

    public function testCreateReservation(): void
    {
        $createCount = 1;
        $reservations = Reservation::factory($createCount)->create()->toArray();
        self::assertCount($createCount, $reservations);
    }

    public function testExistsDuplicateReservation(): void
    {
        Reservation::factory(2)->create([
            'reservation_date' => '2023/1/1',
            'reservation_time' => '11:00:00', 
        ]);
        
    }
}
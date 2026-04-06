<?php

namespace Tests\Feature\Api\V1;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_day_week_and_month_statistics(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-04-08 12:00:00'));

        Ticket::factory()->create([
            'status' => 'new',
            'date_at' => now()->subHours(2),
        ]);

        Ticket::factory()->create([
            'status' => 'processed',
            'date_at' => now()->subDays(2),
        ]);

        Ticket::factory()->create([
            'status' => 'at work',
            'date_at' => now()->subDays(6),
        ]);

        Ticket::factory()->create([
            'status' => 'new',
            'date_at' => now()->subMonths(2),
        ]);

        $day = $this->getJson('/api/v1/tikets/statistics?period=day');
        $day->assertOk();
        $day->assertJsonPath('data.period', 'day');
        $day->assertJsonPath('data.total', 1);
        $day->assertJsonPath('data.by_status.new', 1);
        $day->assertJsonPath('data.by_status.processed', 0);

        $week = $this->getJson('/api/v1/tikets/statistics?period=week');
        $week->assertOk();
        $week->assertJsonPath('data.period', 'week');
        $week->assertJsonPath('data.total', 2);
        $week->assertJsonPath('data.by_status.new', 1);
        $week->assertJsonPath('data.by_status.processed', 1);

        $month = $this->getJson('/api/v1/tikets/statistics?period=month');
        $month->assertOk();
        $month->assertJsonPath('data.period', 'month');
        $month->assertJsonPath('data.total', 3);
        $month->assertJsonPath('data.by_status.new', 1);
        $month->assertJsonPath('data.by_status.processed', 1);
        $month->assertJsonFragment(['at work' => 1]);

        Carbon::setTestNow();
    }

    public function test_it_returns_validation_error_for_invalid_period(): void
    {
        $response = $this->getJson('/api/v1/tikets/statistics?period=year');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['period']);
    }
}

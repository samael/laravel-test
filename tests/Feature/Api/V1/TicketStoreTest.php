<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_ticket_successfully(): void
    {
        $response = $this->postJson('/api/v1/tickets', [
            'name' => 'John Doe',
            'phone' => '+380991112233',
            'email' => 'john@example.com',
            'topic' => 'Billing issue',
            'body' => 'Need help with billing details.',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'topic',
                    'body',
                    'status',
                    'date_at',
                    'customer' => ['id', 'name', 'phone', 'email'],
                    'created_at',
                    'updated_at',
                ],
            ]);

        $response->assertJsonPath('data.status', 'new');
        $response->assertJsonPath('data.customer.email', 'john@example.com');

        $this->assertDatabaseHas('customers', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'phone' => '+380991112233',
        ]);

        $customer = Customer::query()->where('email', 'john@example.com')->firstOrFail();

        $this->assertDatabaseHas('tickets', [
            'client_id' => $customer->id,
            'topic' => 'Billing issue',
            'status' => 'new',
        ]);
    }

    public function test_it_returns_validation_errors_for_invalid_payload(): void
    {
        $response = $this->postJson('/api/v1/tickets', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'phone', 'email', 'topic', 'body']);
    }

    public function test_it_blocks_second_ticket_for_same_email(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'phone' => '+380991112233',
        ]);

        Ticket::factory()->create([
            'client_id' => $customer->id,
            'status' => 'new',
        ]);

        $response = $this->postJson('/api/v1/tickets', [
            'name' => 'John Doe',
            'phone' => '+380000000001',
            'email' => 'john@example.com',
            'topic' => 'Duplicate email ticket',
            'body' => 'Should fail because email is already used for a ticket.',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_blocks_second_ticket_for_same_phone(): void
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'phone' => '+380991112233',
        ]);

        Ticket::factory()->create([
            'client_id' => $customer->id,
            'status' => 'new',
        ]);

        $response = $this->postJson('/api/v1/tickets', [
            'name' => 'John Doe',
            'phone' => '+380991112233',
            'email' => 'other@example.com',
            'topic' => 'Duplicate phone ticket',
            'body' => 'Should fail because phone is already used for a ticket.',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone']);
    }
}

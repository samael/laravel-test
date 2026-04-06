<?php

namespace Tests\Feature\Api\V1;

use App\Models\Customer;
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
                'data' => ['ticket_id', 'status', 'customer_id'],
            ]);

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
}

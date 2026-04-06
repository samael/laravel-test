<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Ticket>
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Customer::factory(),
            'topic' => fake()->sentence(4),
            'body' => fake()->paragraph(4),
            'status' => fake()->randomElement(['new', 'at work', 'processed']),
            'date_at' => fake()->dateTimeBetween('-1 month', '+1 month'),
        ];
    }
}

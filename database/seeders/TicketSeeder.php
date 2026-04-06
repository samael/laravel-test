<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::query()->pluck('id');

        if ($customers->isEmpty()) {
            Ticket::factory(30)->create();

            return;
        }

        Ticket::factory(30)->create([
            'client_id' => fn () => $customers->random(),
        ]);
    }
}

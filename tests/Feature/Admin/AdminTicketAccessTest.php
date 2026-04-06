<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminTicketAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_ticket_panel(): void
    {
        $this->get('/admin/tickets')->assertForbidden();
    }

    public function test_manager_can_access_admin_ticket_panel(): void
    {
        Role::firstOrCreate(['name' => 'manager']);

        $user = User::factory()->create();
        $user->assignRole('manager');

        $this->actingAs($user)
            ->get('/admin/tickets')
            ->assertOk()
            ->assertSeeText('Tickets');
    }

    public function test_admin_can_access_admin_ticket_panel(): void
    {
        Role::firstOrCreate(['name' => 'admin']);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/admin/tickets')
            ->assertOk()
            ->assertSeeText('Tickets');
    }
}

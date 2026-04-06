<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminTicketPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ticket_list_filters_by_status_email_phone_and_date(): void
    {
        Carbon::setTestNow('2026-04-10 12:00:00');

        $admin = $this->createAdminUser();

        $targetCustomer = Customer::factory()->create([
            'email' => 'target@example.com',
            'phone' => '+3800011122',
        ]);

        $otherCustomer = Customer::factory()->create([
            'email' => 'other@example.com',
            'phone' => '+3800099999',
        ]);

        $matched = Ticket::factory()->create([
            'client_id' => $targetCustomer->id,
            'status' => 'new',
            'topic' => 'Matched Ticket',
            'date_at' => now()->subDay(),
        ]);

        Ticket::factory()->create([
            'client_id' => $targetCustomer->id,
            'status' => 'processed',
            'topic' => 'Wrong Status',
            'date_at' => now()->subDay(),
        ]);

        Ticket::factory()->create([
            'client_id' => $otherCustomer->id,
            'status' => 'new',
            'topic' => 'Wrong Contact',
            'date_at' => now()->subDay(),
        ]);

        Ticket::factory()->create([
            'client_id' => $targetCustomer->id,
            'status' => 'new',
            'topic' => 'Out of Range',
            'date_at' => now()->subDays(10),
        ]);

        $response = $this->actingAs($admin)->get('/admin/tickets?status=new&email=target@example.com&phone=11122&date_from=2026-04-08&date_to=2026-04-10');

        $response
            ->assertOk()
            ->assertSeeText($matched->topic)
            ->assertDontSeeText('Wrong Status')
            ->assertDontSeeText('Wrong Contact')
            ->assertDontSeeText('Out of Range');

        Carbon::setTestNow();
    }

    public function test_admin_can_view_ticket_details_and_update_status(): void
    {
        $admin = $this->createAdminUser();
        $ticket = Ticket::factory()->create([
            'status' => 'new',
            'topic' => 'Status Update Ticket',
        ]);

        $this->actingAs($admin)
            ->get('/admin/tickets/' . $ticket->id)
            ->assertOk()
            ->assertSeeText('Status Update Ticket')
            ->assertSeeText('Update status');

        $this->actingAs($admin)
            ->patch('/admin/tickets/' . $ticket->id . '/status', ['status' => 'processed'])
            ->assertRedirect('/admin/tickets/' . $ticket->id);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'processed',
        ]);
    }

    public function test_admin_can_download_ticket_file_and_cannot_download_foreign_file(): void
    {
        $admin = $this->createAdminUser();

        $ticket = Ticket::factory()->create();
        $foreignTicket = Ticket::factory()->create();

        $media = $ticket
            ->addMediaFromString('test-content')
            ->usingFileName('evidence.txt')
            ->toMediaCollection('tickets_files');

        $foreignMedia = $foreignTicket
            ->addMediaFromString('foreign-content')
            ->usingFileName('foreign.txt')
            ->toMediaCollection('tickets_files');

        $this->actingAs($admin)
            ->get('/admin/tickets/' . $ticket->id . '/files/' . $media->id . '/download')
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->actingAs($admin)
            ->get('/admin/tickets/' . $ticket->id . '/files/' . $foreignMedia->id . '/download')
            ->assertNotFound();
    }

    private function createAdminUser(): User
    {
        Role::firstOrCreate(['name' => 'admin']);

        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }
}

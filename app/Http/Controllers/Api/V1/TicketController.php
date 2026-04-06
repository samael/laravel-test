<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Store a ticket from feedback widget.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'topic' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $customer = Customer::query()->firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'],
            ]
        );

        if ($customer->name !== $validated['name'] || $customer->phone !== $validated['phone']) {
            $customer->fill([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
            ])->save();
        }

        $ticket = Ticket::query()->create([
            'client_id' => $customer->id,
            'topic' => $validated['topic'],
            'body' => $validated['body'],
            'status' => 'new',
            'date_at' => now(),
        ]);

        return response()->json([
            'message' => 'Thank you! Your request has been sent. We will contact you soon.',
            'data' => [
                'ticket_id' => $ticket->id,
                'status' => $ticket->status,
                'customer_id' => $customer->id,
            ],
        ], 201);
    }
}

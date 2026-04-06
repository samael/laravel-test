<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TicketResource;
use App\Http\Resources\V1\TicketStatisticsResource;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        $emailAlreadyUsed = Ticket::query()
            ->whereHas('client', function ($query) use ($validated): void {
                $query->where('email', $validated['email']);
            })
            ->exists();

        $phoneAlreadyUsed = Ticket::query()
            ->whereHas('client', function ($query) use ($validated): void {
                $query->where('phone', $validated['phone']);
            })
            ->exists();

        if ($emailAlreadyUsed || $phoneAlreadyUsed) {
            $errors = [];

            if ($emailAlreadyUsed) {
                $errors['email'] = ['Only one ticket is allowed per email.'];
            }

            if ($phoneAlreadyUsed) {
                $errors['phone'] = ['Only one ticket is allowed per phone.'];
            }

            throw ValidationException::withMessages($errors);
        }

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

        return (new TicketResource($ticket->load('client')))
            ->additional([
                'message' => 'Thank you! Your request has been sent. We will contact you soon.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get ticket statistics by period.
     */
    public function statistics(Request $request): TicketStatisticsResource
    {
        $validated = $request->validate([
            'period' => ['nullable', 'in:day,week,month'],
        ]);

        $period = $validated['period'] ?? 'day';
        [$from, $to] = Ticket::statisticsPeriodBounds($period);

        $query = Ticket::query()->forStatisticsPeriod($period);
        $total = (clone $query)->count();

        $statusCounts = (clone $query)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return new TicketStatisticsResource([
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'total' => $total,
            'by_status' => [
                'new' => (int) ($statusCounts['new'] ?? 0),
                'at work' => (int) ($statusCounts['at work'] ?? 0),
                'processed' => (int) ($statusCounts['processed'] ?? 0),
            ],
        ]);
    }
}

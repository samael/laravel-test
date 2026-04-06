<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TicketAdminController extends Controller
{
    /**
     * Display ticket list with filters.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:new,at work,processed'],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $tickets = Ticket::query()
            ->with('client')
            ->when($validated['status'] ?? null, function ($query, $status): void {
                $query->where('status', $status);
            })
            ->when($validated['email'] ?? null, function ($query, $email): void {
                $query->whereHas('client', function ($clientQuery) use ($email): void {
                    $clientQuery->where('email', 'like', "%{$email}%");
                });
            })
            ->when($validated['phone'] ?? null, function ($query, $phone): void {
                $query->whereHas('client', function ($clientQuery) use ($phone): void {
                    $clientQuery->where('phone', 'like', "%{$phone}%");
                });
            })
            ->when($validated['date_from'] ?? null, function ($query, $dateFrom): void {
                $query->whereDate('date_at', '>=', $dateFrom);
            })
            ->when($validated['date_to'] ?? null, function ($query, $dateTo): void {
                $query->whereDate('date_at', '<=', $dateTo);
            })
            ->orderByDesc('date_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'filters' => $validated,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    /**
     * Display a single ticket details page.
     */
    public function show(Ticket $ticket): View
    {
        $ticket->load(['client', 'media']);

        return view('admin.tickets.show', [
            'ticket' => $ticket,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,at work,processed'],
        ]);

        $ticket->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully.');
    }

    /**
     * Download media file attached to ticket.
     */
    public function downloadFile(Ticket $ticket, Media $media): BinaryFileResponse
    {
        if ($media->model_type !== Ticket::class || (int) $media->model_id !== $ticket->id) {
            abort(404);
        }

        return response()->download($media->getPath(), $media->file_name, [
            'Content-Type' => $media->mime_type ?? 'application/octet-stream',
        ]);
    }

    /**
     * Available ticket statuses.
     *
     * @return list<string>
     */
    private function statusOptions(): array
    {
        return ['new', 'at work', 'processed'];
    }
}

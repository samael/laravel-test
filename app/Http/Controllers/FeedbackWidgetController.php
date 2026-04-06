<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FeedbackWidgetController extends Controller
{
    /**
     * Show feedback widget page.
     */
    public function index(Request $request): Response
    {
        $frameAncestors = env('FEEDBACK_WIDGET_FRAME_ANCESTORS', '*');

        return response()
            ->view('feedback-widget', [
                'embedded' => $request->boolean('embedded', true),
            ])
            ->header('Content-Security-Policy', "frame-ancestors {$frameAncestors}")
            ->header('X-Frame-Options', 'ALLOWALL');
    }

    /**
     * Handle feedback form submission.
     */
    public function store(Request $request): RedirectResponse
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

        Ticket::query()->create([
            'client_id' => $customer->id,
            'topic' => $validated['topic'],
            'body' => $validated['body'],
            'status' => 'new',
            'date_at' => now(),
        ]);

        return redirect()
            ->route('feedback-widget.index')
            ->with('success', 'Thank you! Your request has been sent. We will contact you soon.');
    }
}
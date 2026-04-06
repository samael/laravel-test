<?php

use App\Http\Controllers\Api\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/tickets', [TicketController::class, 'store'])
        ->name('api.v1.tickets.store');
});

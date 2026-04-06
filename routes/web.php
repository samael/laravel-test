<?php

use App\Http\Controllers\Admin\TicketAdminController;
use App\Http\Controllers\FeedbackWidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('feedback-widget.index');
});

Route::get('/feedback-widget', [FeedbackWidgetController::class, 'index'])
    ->name('feedback-widget.index');

Route::view('/api/docs', 'api-docs')
    ->name('api.docs');

Route::prefix('admin')
    ->middleware('role:manager,admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/tickets', [TicketAdminController::class, 'index'])
            ->name('tickets.index');

        Route::get('/tickets/{ticket}', [TicketAdminController::class, 'show'])
            ->name('tickets.show');

        Route::patch('/tickets/{ticket}/status', [TicketAdminController::class, 'updateStatus'])
            ->name('tickets.update-status');

        Route::get('/tickets/{ticket}/files/{media}/download', [TicketAdminController::class, 'downloadFile'])
            ->name('tickets.files.download');
    });

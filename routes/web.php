<?php

use App\Http\Controllers\FeedbackWidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/feedback-widget', [FeedbackWidgetController::class, 'index'])
    ->name('feedback-widget.index');

Route::post('/feedback-widget', [FeedbackWidgetController::class, 'store'])
    ->name('feedback-widget.store');

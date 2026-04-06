<?php

namespace App\Http\Controllers;

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
}
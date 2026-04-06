<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackWidgetPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_widget_page_is_accessible_with_embed_headers(): void
    {
        $response = $this->get('/feedback-widget');

        $response
            ->assertOk()
            ->assertHeader('Content-Security-Policy')
            ->assertHeader('X-Frame-Options', 'ALLOWALL')
            ->assertSeeText('Customer feedback');
    }
}

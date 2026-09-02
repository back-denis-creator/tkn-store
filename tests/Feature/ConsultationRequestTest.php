<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendTelegramNotification;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * The "order a consultation" form on the homepage (ConsultationModal.vue)
 * posts here purely to trigger a team-facing Telegram notification — it
 * doesn't persist anything, the Google Sheet integration remains the lead
 * record.
 *
 * See OrderTelegramNotificationTest for why CSRF is disabled per-test here.
 */
class ConsultationRequestTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_submitting_a_consultation_request_queues_a_telegram_notification(): void
    {
        Queue::fake();

        $response = $this->postJson('/consultation-requests', [
            'name' => 'Denis',
            'phone' => '+380501234567',
            'service' => 'Пошиття штор',
        ]);

        $response->assertSuccessful();
        $response->assertJson(['success' => true]);

        Queue::assertPushed(SendTelegramNotification::class, function ($job) {
            return str_contains($job->message, 'Заявка на консультацію')
                && str_contains($job->message, 'Denis')
                && str_contains($job->message, '+380501234567')
                && str_contains($job->message, 'Пошиття штор');
        });
    }

    public function test_service_is_optional(): void
    {
        Queue::fake();

        $this->postJson('/consultation-requests', [
            'name' => 'Olena',
            'phone' => '+380507654321',
        ])->assertSuccessful();

        Queue::assertPushed(SendTelegramNotification::class, function ($job) {
            return ! str_contains($job->message, 'Послуга');
        });
    }

    public function test_name_and_phone_are_required(): void
    {
        Queue::fake();

        $this->postJson('/consultation-requests', [])
            ->assertInvalid(['name', 'phone']);

        Queue::assertNothingPushed();
    }
}

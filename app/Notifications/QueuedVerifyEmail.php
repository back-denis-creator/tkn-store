<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Same notification as the base VerifyEmail (including the Ukrainian mail
 * built by VerifyEmail::toMailUsing() in AppServiceProvider, which this
 * class inherits since it doesn't redeclare $toMailCallback) — just queued,
 * so the registration request doesn't wait on the SMTP round trip.
 */
class QueuedVerifyEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;
}

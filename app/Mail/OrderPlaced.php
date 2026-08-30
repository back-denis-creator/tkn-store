<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderPlaced extends Mailable
{
    /**
     * Sent synchronously (no ShouldQueue) — a queue worker may not be
     * running in every environment, and order confirmation shouldn't
     * silently depend on one being up.
     */
    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Замовлення №{$this->order->id} прийнято — Casanel",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-placed',
            with: ['order' => $this->order->load('orderItems')],
        );
    }
}

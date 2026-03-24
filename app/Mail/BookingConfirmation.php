<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public $services; // Collection từ DB query

    public function __construct(Order $order, $services = null)
    {
        $this->order    = $order;
        $this->services = $services ?? collect();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Xác nhận đặt phòng #' . $this->order->transfer_code . ' - Resort Pro',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
        );
    }
}
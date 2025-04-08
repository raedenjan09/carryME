```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    protected $order;
    protected $pdfPath;

    public function __construct($order, $pdfPath)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Order Status Update - BagXury')
            ->greeting('Hello ' . $this->order->user->name)
            ->line('Your order status has been updated to: ' . $this->order->status)
            ->line('Order ID: ' . $this->order->id)
            ->line('Thank you for shopping with BagXury!');

        if ($this->order->status === 'completed') {
            $message->attach($this->pdfPath, [
                'as' => 'receipt.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $message;
    }
}
```
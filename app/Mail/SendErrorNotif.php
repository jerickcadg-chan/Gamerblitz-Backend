<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendErrorNotif extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $errMessage;

    /**
     * Create a new errMessage instance.
     *
     * @return void
     */
    public function __construct($order, $errMessage)
    {
        $this->order = $order;
        $this->errMessage = $errMessage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Ada Orderan Error #'.$this->order->code)->view('mail.error-notif');
    }
}

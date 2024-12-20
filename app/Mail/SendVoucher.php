<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendVoucher extends Mailable
{
    use Queueable, SerializesModels;

    public $voucher;
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order, $voucher)
    {
        $this->order = $order;
        $this->voucher = $voucher;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('MitraGamers - Voucher Receipt #'.$this->order->code)->view('mail.voucher');
    }
}

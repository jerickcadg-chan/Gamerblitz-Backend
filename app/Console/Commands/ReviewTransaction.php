<?php

namespace App\Console\Commands;

use App\Data\LapakGaming\Transaction;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Console\Command;

class ReviewTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:review-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = app()->getLocale(); // 'id' atau 'en'

        $bodies = [
            'id' => [
                'Pelayanan sangat cepat dan memuaskan!',
                'Proses top-up lancar tanpa kendala, mantap!',
                'Sangat direkomendasikan buat yang butuh topup cepat.',
                'Respon admin cepat, transaksi aman dan terpercaya.',
                'Langganan terus di sini karena pelayanannya terbaik.',
                'Harga murah, pengiriman cepat, puas banget!',
                'Top-up sukses dalam hitungan detik, luar biasa!',
                'Admin ramah, proses cepat, pasti repeat order.',
                'Sudah beberapa kali beli, selalu memuaskan!',
                'Terpercaya banget, gak pernah gagal.',
            ],
            'en' => [
                'Very fast and satisfying service!',
                'Smooth top-up process, awesome!',
                'Highly recommended for fast top-ups.',
                'Quick response from admin, safe and reliable.',
                'Always my go-to, best service ever.',
                'Cheap prices, fast delivery, very satisfied!',
                'Top-up completed in seconds, amazing!',
                'Friendly admin, fast process, will order again.',
                'Bought multiple times, always great!',
                'Really trustworthy, never disappointed.',
            ]
        ];

        $orders = Order::doesntHave('reviews')->get();

        foreach ($orders as $order) {
            Review::create([
                'order_id' => $order->id,
                'product_id' => $order->product_id,
                'star' => 5,
                'body' => collect($bodies[$locale])->random()
            ]);
        }
    }
}

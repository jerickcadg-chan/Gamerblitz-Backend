<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\Order;
use App\Services\DepositService;
use Illuminate\Console\Command;

class SetExpiredDeposit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expired:deposit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set expired deposit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $deposits = Deposit::where('status', Order::PENDING)
            ->where('expired_at', '<=', now()->format('Y-m-d H:i:s'))
            ->get();

        if ($deposits->count() > 0) {
            foreach ($deposits as $deposit) {
                DepositService::updateStatus($deposit, Order::EXPIRED);

                $this->info($deposit->code);
            }
        }
    }
}

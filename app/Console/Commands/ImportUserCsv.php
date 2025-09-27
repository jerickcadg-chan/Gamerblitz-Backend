<?php

namespace App\Console\Commands;

use App\Constants\DefaultRole;
use App\Models\Affiliate;
use App\Models\Balance;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportUserCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-user-csv {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import user data from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get file name from artisan option
        $filename = $this->option('file');

        if (!$filename) {
            throw new \InvalidArgumentException("You must provide a CSV file name using --file, e.g. --file=users_1.csv");
        }

        // Default path: database/imports
        $file = database_path("imports/{$filename}");

        if (!file_exists($file) || !is_readable($file)) {
            throw new \Exception("CSV file not found or not readable: $file");
        }

        $data = [];

        if (($handle = fopen($file, 'r')) !== false) {
            $header = null;

            while (($row = fgetcsv($handle, 0, ',', '"')) !== false) {
                if ($row === [null] || empty(array_filter($row))) {
                    continue;
                }

                if (!$header) {
                    $header = $row;
                    continue;
                }

                // Normalize row column count with header
                $row = array_pad($row, count($header), null);
                $row = array_slice($row, 0, count($header));

                $data[] = array_combine($header, $row);
            }

            fclose($handle);
        }

        $count = count($data);
        $this->info("Start importing $count users from file: $file");

        $bar = $this->getOutput()->createProgressBar($count);
        $bar->start();

        foreach ($data as $item) {
            $this->createUser($item);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Import finished ✅");
    }

    /**
     * Create or update user from CSV row
     *
     * @param array $item
     * @return void
     */
    private function createUser(array $item): void
    {
        // Ensure keys exist (in case CSV headers differ)
        $username     = $item['Username'] ?? null;
        $phone        = $item['Whatsapp'] ?? null;
        $level        = $item['Level'] ?? null;
        $balanceRaw   = $item['Balance'] ?? null;
        $affiliateRaw = $item['Affiliate Commission'] ?? null;

        if (!$phone) {
            $this->warn("Skipped user without Whatsapp number. Data: " . json_encode($item));
            return;
        }

        // Parse numeric values (remove symbols except digits and dot)
        $balance      = (float) preg_replace('/[^\d.]/', '', (string) $balanceRaw);
        $affiliateCom = (float) preg_replace('/[^\d.]/', '', (string) $affiliateRaw);

        // Create or update user
        $user = User::updateOrCreate(
            ['phone_number' => $phone],
            [
                'name'         => $username,
                'email'        => $this->generateEmail($username),
                'phone_number' => $phone,
                'password'     => Hash::make(Str::random(12)),
            ]
        );

        // Assign role according to Level
        $user->assignRole($this->mapRole($level));

        // Sync balance
        if ($balance > 0) {
            $this->syncBalance($user, $balance);
        }

        // Sync affiliate commission
        if ($affiliateCom > 0) {
            $this->syncAffiliate($user, $affiliateCom);
        }
    }

    /**
     * Sync user balance (idempotent)
     *
     * @param User $user
     * @param float $amount
     * @return void
     */
    private function syncBalance(User $user, float $amount): void
    {
        $balanceModel = Balance::query()
            ->lockForUpdate()
            ->firstOrCreate(
                ['user_id' => $user->id],
                ['amount'  => 0]
            );

        // Update balance via BalanceService
        BalanceService::update($balanceModel, [
            'balanceable_type' => $user->getMorphClass(),
            'balanceable_id'   => $user->getKey(),
            'amount'           => $amount,
            'description'      => "Topup Balance by Import Seeder",
        ]);
    }

    /**
     * Sync user affiliate data (idempotent)
     *
     * @param User $user
     * @param float $commission
     * @return void
     */
    private function syncAffiliate(User $user, float $commission): void
    {
        Affiliate::updateOrCreate(
            ['user_id' => $user->id],
            [
                'code'    => $this->generateAffiliateCode($user->name),
                'status'  => 'active',
                'balance' => $commission,
            ]
        );
    }

    /**
     * Generate unique email from username
     *
     * @param string|null $name
     * @return string
     */
    private function generateEmail(?string $name): string
    {
        // if already a valid email, return as is
        if ($name && filter_var($name, FILTER_VALIDATE_EMAIL)) {
            return Str::lower($name); // bisa lowercase biar konsisten
        }

        $base  = Str::lower(preg_replace('/\s+/', '', (string) $name));
        $email = $base . '@example.com';

        $i = 1;
        while (User::where('email', $email)->exists()) {
            $email = $base . $i . '@example.com';
            $i++;
        }

        return $email;
    }

    /**
     * Map role string from CSV to role constant
     *
     * @param string|null $role
     * @return string
     */
    private function mapRole(?string $role): string
    {
        return match ($role) {
            'Member' => DefaultRole::CUSTOMER,
            'Silver' => DefaultRole::RESELLER_SILVER,
            'Gold'   => DefaultRole::RESELLER_GOLD,
            'VIP'    => DefaultRole::RESELLER_VIP,
            default  => DefaultRole::CUSTOMER,
        };
    }

    /**
     * Generate unique affiliate code (from base name or random)
     *
     * @param string|null $base
     * @return string
     */
    private function generateAffiliateCode(?string $base = null): string
    {
        if ($base) {
            $base      = Str::upper(preg_replace('/\s+/', '', $base));
            $candidate = $base;
            $i         = 1;

            while (Affiliate::where('code', $candidate)->exists()) {
                $candidate = $base . $i;
                $i++;

                if ($i > 9999) {
                    $candidate = Str::upper(Str::random(8));
                    break;
                }
            }

            return $candidate;
        }

        do {
            $code = Str::upper(Str::random(8));
        } while (Affiliate::where('code', $code)->exists());

        return $code;
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * For development and testing purchase only
 *
 */
class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(PermissionsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(PaymentMethodsTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
    }
}

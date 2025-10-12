<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::table('permissions')->truncate();

        $this->createPermissions();

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }

    public function createPermissions()
    {
        $permissions = [
            'View Dashboard',

            'View Order',
            'Process Order',

            'View Transaction Statistic',
            'View User Statistic',

            'View Product Category',
            'Create Product Category',
            'Edit Product Category',
            'Delete Product Category',

            'View Product',
            'Create Product',
            'Edit Product',
            'Delete Product',

            'View Product Item',
            'Create Product Item',
            'Edit Product Item',
            'Delete Product Item',

            'View Voucher',
            'Create Voucher',
            'Edit Voucher',
            'Delete Voucher',

            'View Discount',
            'Create Discount',
            'Edit Discount',
            'Delete Discount',

            'View Transaction',

            'View Transaction Report',
            'View User Report',
            'View Product Report',

            'View Statistic',

            'View Deposit',
            'Edit Deposit',

            'View User',
            'Create User',
            'Edit User',
            'Delete User',

            'View Guest',
            'Create Guest',
            'Edit Guest',
            'Delete Guest',

            'View Customer',
            'Create Customer',
            'Edit Customer',
            'Delete Customer',

            'View Discount',
            'Create Discount',
            'Edit Discount',
            'Delete Discount',

            'View Slider',
            'Create Slider',
            'Edit Slider',
            'Delete Slider',

            'View Role',
            'Create Role',
            'Edit Role',
            'Delete Role',

            'View Flash Sales',
            'Create Flash Sales',
            'Edit Flash Sales',
            'Delete Flash Sales',

            'View Setting',
            'Create Setting',
            'Edit Setting',
            'Delete Setting',

            'View Blog',
            'Create Blog',
            'Edit Blog',
            'Delete Blog',

            'View Exchange Rate',
            'Create Exchange Rate',
            'Edit Exchange Rate',
            'Delete Exchange Rate',
        ];

        foreach ($permissions as $permission){
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}

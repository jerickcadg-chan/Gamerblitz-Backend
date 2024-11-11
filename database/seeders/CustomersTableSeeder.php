<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i=1; $i <= 50; $i++) {
            $user = User::create([
                'name' => $faker->unique()->name,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'phone_number' => '62'. rand(100000000, 99999999),
                'password' => '12345678',
            ]);

            $user->assignRole(\Spatie\Permission\Models\Role::where('name', 'Customer')->first());
        }
    }
}

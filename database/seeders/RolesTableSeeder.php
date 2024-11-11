<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \Illuminate\Support\Facades\DB::table('roles')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $roles = $this->getRoles();

        foreach ($roles as $role){
            Role::create([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }

    function getRoles()
    {
        $reflect = new \ReflectionClass(\App\Constants\DefaultRole::class);
        return $reflect->getConstants();
    }
}

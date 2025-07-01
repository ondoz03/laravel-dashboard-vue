<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MasterItem;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
// Seed roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // Create super admin user
        $this->call(SuperAdminUserSeeder::class);

        // Create all-access user
        $this->call(AllAccessUserSeeder::class);

        User::factory(10)->create();

        // Create 100 dummy master items
        MasterItem::factory(100)->create();
    }
}

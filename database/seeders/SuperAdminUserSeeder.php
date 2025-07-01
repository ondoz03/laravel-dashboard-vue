<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        $user = User::where('email', 'ondozwahyudi@gmail.com')->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'ondozwahyudi@gmail.com',
                'password' => Hash::make('password'), // Default password, should be changed after first login
            ]);
        }

        // Assign super-admin role
        $user->assignRole('super-admin');
    }
}

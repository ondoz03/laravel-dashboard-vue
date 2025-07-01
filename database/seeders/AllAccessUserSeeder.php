<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AllAccessUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create all-access user
        $user = User::where('email', 'allaccessuser@example.com')->first();

        if (!$user) {
            $user = User::create([
                'name' => 'All Access User',
                'email' => 'allaccessuser@example.com',
                'password' => Hash::make('password'), // Default password, should be changed after first login
            ]);
        }

        // Assign super-admin role to give access to all modules
        $user->assignRole('super-admin');
    }
}

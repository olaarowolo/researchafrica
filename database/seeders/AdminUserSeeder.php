<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'tech@olaarowolo.com'],
            [
                'name' => 'Admin Tech',
                'password' => Hash::make('password123'), // You can change the default password here
                // Assign role id 1 to make this user admin
            ]
        );

        // Assign role id 1 to the user to make them admin
        $user = User::where('email', 'tech@olaarowolo.com')->first();
        if ($user) {
            $user->roles()->sync([1]);
        }
    }
}

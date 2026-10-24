<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            'shahimahesh@gmail.com',
            'shahimahesh4@gmail.com',
        ];

        foreach ($admins as $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Mahesh Shahi',
                    'password' => Hash::make('Mahesh@9843##'),
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                    'role' => 'super_admin',
                ]
            );
        }
    }
}

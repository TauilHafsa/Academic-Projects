<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@velocite.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        UserProfile::create([
            'user_id' => $admin->id,
            'city' => 'Paris',
            'phone_number' => '+33 1 23 45 67 89',
        ]);

        // Create Agent Users
        $agentCities = [
            'Lyon' => '+33 4 72 10 30 30',
            'Marseille' => '+33 4 91 13 89 00',
            'Bordeaux' => '+33 5 56 00 33 33',
        ];

        $i = 1;
        foreach ($agentCities as $city => $phone) {
            $agent = User::create([
                'name' => "Agent $city",
                'email' => "agent{$i}@velocite.com",
                'password' => Hash::make('password'),
                'role' => 'agent',
                'email_verified_at' => now(),
            ]);

            UserProfile::create([
                'user_id' => $agent->id,
                'city' => $city,
                'phone_number' => $phone,
                'bio' => "Vélocité agent responsible for the $city region."
            ]);

            $i++;
        }
    }
}

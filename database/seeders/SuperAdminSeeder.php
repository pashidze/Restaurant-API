<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'Super@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('superadmin'),
            'pin_code' => Hash::make('0000'),
            'role_id' => 1,
            'remember_token' => null,
        ]);
    }
}

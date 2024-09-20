<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = 'Admin';
        $email = 'admin@gmail.com';
        $password = '12345678';

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name, 
                'password' => Hash::make($password)
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Chaeza Ibnu Akbar',
            'email' => 'chaezaibnuakbar@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_logged_in' => false,
        ]);

        \App\Models\User::create([
            'name' => 'Kasir Satu',
            'email' => 'kasir@example.com',
            'password' => bcrypt('password123'),
            'role' => 'kasir',
            'phone' => '089876543210',
            'is_logged_in' => false,
        ]);

        $this->call([
            DrugSeeder::class
        ]);
    }
}

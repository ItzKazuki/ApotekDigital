<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user if none exists';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cek apakah sudah ada admin
        $existingAdmin = User::where('role', 'admin')->first();

        if ($existingAdmin) {
            $this->error('❌ Admin account already exists.');
            $this->info("💡 Existing admin email: {$existingAdmin->email}");
            return Command::FAILURE;
        }

        // Minta input dari user
        $name = $this->ask('Nama admin');
        $email = $this->ask('Email admin');
        $phone = $this->ask('Nomor telepon admin');
        $password = $this->secret('Password admin');

        // Simpan akun admin
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info("✅ Admin account created successfully for {$email}");
        return Command::SUCCESS;
    }
}

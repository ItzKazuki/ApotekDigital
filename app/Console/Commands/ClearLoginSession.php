<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearLoginSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-login-session';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing all login session in database trough command.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Reset is_logged_in
        DB::table('users')->update(['is_logged_in' => 0]);

        // Hapus semua session
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->truncate();
        } else {
            $this->warn('Tabel sessions tidak ditemukan, pastikan session disimpan di database.');
        }

        $this->info('✅ Semua user berhasil di-logout dan status login direset.');
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('chezzy:migration')]
#[Description('Menjalankan migrasi database dan seeder')]
class ChezzyMigration extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Menjalankan migrasi database...');
        $migrate = $this->callSilent('migrate', ['--force' => true]);

        if ($migrate === 0) {
            $this->info('✔️  Migrasi database berhasil.');
        } else {
            $this->error('❌  Migrasi gagal.');

            return;
        }

        $this->info('Menjalankan seeder...');
        $seeder = $this->callSilent('db:seed', ['--class' => 'UserRolePermissionSeeder']);

        if ($seeder === 0) {
            $this->info('✔️  Seeder berhasil dijalankan.');
        } else {
            $this->error('❌  Seeder gagal.');
        }

        $this->info('✅ Proses migrasi & seeder selesai!');
    }
}

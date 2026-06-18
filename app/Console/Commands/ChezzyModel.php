<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('chezzy:model')]
#[Description('Membuat satu atau beberapa model dengan migration dan fillable default')]
class ChezzyModel extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $models = [];

        while (true) {
            $modelName = $this->ask("\033[36mMasukkan nama model yang ingin Anda buat \033[37m[\033[0m\033[33mkosongkan untuk selesai\033[0m\033[37m]\033[0m");

            if (empty($modelName)) {
                break;
            }

            // Pastikan nama model PascalCase (Opsional tapi direkomendasikan)
            $modelName = Str::studly($modelName);

            if (in_array($modelName, $models)) {
                $this->info("\033[31m Model '$modelName' sudah ditambahkan sebelumnya. Masukkan model lain.\033[0m");
            } else {
                $models[] = $modelName;
            }
        }

        if (empty($models)) {
            $this->info("\033[31m Tidak ada model yang ditambahkan. Proses dibatalkan.\033[0m");

            return;
        }

        $this->info(" \033[37mModel yang akan Dibuat :\033[0m");
        foreach ($models as $index => $model) {
            $this->line(" [\033[33m".($index + 1)."\033[0m] \033[33m$model\033[0m");
        }

        if (! $this->confirm("\033[36mApakah Anda ingin melanjutkan pembuatan model ini\033[0m \033[37m?", true)) {
            $this->warn("\033[31m Proses dibatalkan.\033[0m");

            return;
        }

        $totalModels = count($models);
        $progressWidth = 30;
        $index = 0;

        $this->output->write("\033[?25l");

        foreach ($models as $model) {
            $index++;

            $progressBar = str_repeat('=', intval(($index / $totalModels) * $progressWidth)).'>'.str_repeat('-', $progressWidth - intval(($index / $totalModels) * $progressWidth));

            if ($index === $totalModels) {
                $progressText = " $index/$totalModels [$progressBar]  ".intval(($index / $totalModels) * 100)."% \033[36mBerhasil\033[0m";
            } else {
                $progressText = " $index/$totalModels [$progressBar]  ".intval(($index / $totalModels) * 100)."% \033[33mMembuat model $model\033[0m";
            }

            $this->output->write("\033[2K\r".str_pad($progressText, 100));

            // Menggunakan Laravel Artisan Call (Lebih aman dari exec)
            $exitCode = $this->callSilent('make:model', [
                'name' => $model,
                '-m' => true,
            ]);

            if ($exitCode === 0) {
                $this->updateModelFile($model);
            }

            usleep(1000000); // Delay untuk efek animasi (1 detik)
        }

        $this->output->write("\033[?25h");

        $this->newLine(2);
        $this->info("\033[36m Model\033[0m \033[37m[\033[0m\033[36m".implode(', ', $models)."\033[0m\033[37m]\033[0m \033[36mberhasil dibuat dengan standar Laravel 13.\033[0m");
    }

    private function updateModelFile(string $model): void
    {
        $modelFilePath = app_path("Models/{$model}.php");
        $stubPath = app_path('Console/Commands/Template/ModelTemplate.stub');

        // Gunakan facade File dari Laravel untuk operasi file yang lebih aman
        if (File::exists($modelFilePath) && File::exists($stubPath)) {
            $template = File::get($stubPath);
            $modelContent = str_replace('{model}', $model, $template);

            File::put($modelFilePath, $modelContent);
        } else {
            $this->warn("\033[31m \n Gagal menimpa model {$model}. File model atau stub tidak ditemukan.\033[0m");
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

#[Signature('chezzy:resource')]
#[Description('Generate Filament resource for all models except specified ones')]
class ChezzyResource extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $options = ['default' => 'Default', 'simple' => 'Simple'];

        do {
            $selectedOption = strtolower($this->ask(
                "\033[36mResource jenis apa yang ingin anda buat pada batch utama?\033[0m \033[37m[\033[0m\033[33msimple\033[37m] atau\033[0m",
                'default'
            ));

            if (!isset($options[$selectedOption])) {
                $this->info("\033[31m Opsi tidak tersedia, silakan masukkan ulang.\033[0m");
            }

        } while (!isset($options[$selectedOption]));

        $modelsPath = app_path('Models');

        if (!File::isDirectory($modelsPath)) {
            $this->error(" Folder 'app/Models' tidak ditemukan.");
            return;
        }

        $modelFiles = File::files($modelsPath);
        if (empty($modelFiles)) {
            $this->warn(" Tidak ada file model di folder 'app/Models'.");
            return;
        }

        $models = [];
        $skippedModels = [];

        // PENGECEKAN RESOURCE: Filter model yang sudah punya file Resource
        foreach ($modelFiles as $file) {
            $modelName = pathinfo($file, PATHINFO_FILENAME);
            $resourcePath = app_path("Filament/Resources/{$modelName}Resource.php");

            if (File::exists($resourcePath)) {
                $skippedModels[] = $modelName;
            } else {
                $models[] = $modelName;
            }
        }

        if (!empty($skippedModels)) {
            $this->info("\033[33m[INFO] Beberapa model diabaikan karena resource sudah ada:\033[0m " . implode(', ', $skippedModels));
            $this->newLine();
        }

        $alwaysExcludedModels = ['User', 'Role', 'Permission'];

        // Gunakan array_values untuk mereset index kembali ke 0, 1, 2, dst.
        $modelsToProcess = array_values(array_diff($models, $alwaysExcludedModels));

        if (empty($modelsToProcess)) {
            $this->warn(" Saat ini tidak ada model baru yang tersedia untuk dibuatkan resource.");
            return;
        }

        $excludedModels = [];
        if ($this->confirm("\033[36mApakah ada model yang ingin dikecualikan\033[0m \033[37m?\033[0m", false)) {
            $excludedModels = $this->askExcludedModels($modelsToProcess);
        }

        // Reset index lagi setelah dikurangi excluded models
        $modelsToProcess = array_values(array_diff($modelsToProcess, $excludedModels));

        $simpleModels = [];
        // Tanya apakah model yang dikecualikan ingin dibuatkan resource dengan tipe yang BEDA dari pilihan awal
        $altType = $selectedOption === 'default' ? 'simple' : 'default';
        if (!empty($excludedModels) && $this->confirm("\033[36mApakah model yang dikecualikan ingin dibuat resource\033[0m [\033[33m$altType\033[0m] ?", false)) {
            $simpleModels = $excludedModels;
        }

        if (!empty($modelsToProcess)) {
            $this->info("\033[37m Model yang akan dibuat resource\033[0m [\033[33m$selectedOption\033[0m] :");
            foreach ($modelsToProcess as $index => $model) {
                $this->line(" [\033[33m$index\033[0m] \033[33m$model\033[0m");
            }
        }

        $this->newLine();

        if (!empty($simpleModels)) {
            $this->info("\033[37m Model yang akan dibuat resource\033[0m [\033[33m$altType\033[0m] :");
            foreach ($simpleModels as $index => $model) {
                $this->line(" [\033[33m$index\033[0m] \033[33m$model\033[0m");
            }
        }

        if (!$this->confirm("\033[36mLanjutkan proses pembuatan resource\033[0m ?", true)) {
            $this->error("Proses dibatalkan oleh pengguna.");
            return;
        }

        $totalModels = count($modelsToProcess) + count($simpleModels);

        // Cek jika total 0 agar tidak terjadi Division by Zero di progress bar
        if ($totalModels === 0) {
            $this->info("Tidak ada model yang diproses.");
            return;
        }

        $progress = 0;

        foreach ($modelsToProcess as $model) {
            // Gunakan $selectedOption dari input user
            $this->generateResource($model, $selectedOption);
            $progress++;
            $this->displayProgress($progress, $totalModels, $model);
        }

        foreach ($simpleModels as $model) {
            $this->generateResource($model, $altType);
            $progress++;
            $this->displayProgress($progress, $totalModels, $model);
        }

        $this->newLine(2);
        $this->info("\033[36m Semua resource telah berhasil dibuat.\033[0m");
    }

    private function askExcludedModels(array $models): array
    {
        $excludedModels = [];

        while (true) {
            $this->info("\033[37m Model yang tersedia :\033[0m");
            foreach ($models as $index => $model) {
                $this->line(" [\033[33m$index\033[0m] \033[33m$model\033[0m");
            }

            // Trim input untuk membersihkan spasi
            $input = trim($this->ask("\033[36mMasukkan\033[0m [\033[33mnomor\033[0m] \033[36mmodel yang ingin dikecualikan\033[0m [\033[33mpisahkan dengan koma\033[0m]"));

            // Cek identik strict dengan string kosong ('') agar angka '0' tidak dianggap empty
            if ($input === '') {
                break;
            }

            $indexes = explode(',', $input);
            foreach ($indexes as $index) {
                $index = trim($index);
                if (is_numeric($index) && isset($models[$index])) {
                    $excludedModels[] = $models[$index];
                } else {
                    $this->error("Nomor '$index' tidak valid.");
                }
            }

            if (!$this->confirm("\033[36mApakah ada lagi yang ingin dikecualikan\033[0m ?", false)) {
                break;
            }
        }

        return $excludedModels;
    }

    private function generateResource(string $model, string $type): void
    {
        $flags = '--generate --view --no-interaction';
        if ($type === 'simple') {
            $flags .= ' --simple';
        }

        $command = "php artisan make:filament-resource $model $flags";

        // Menggunakan Facade Process bawaan Laravel
        Process::run($command);
    }

    private function displayProgress(int $current, int $total, string $model): void
    {
        $progressWidth = 30;
        $progress = ($current / $total) * 100;
        $filledBars = (int) round(($progress / 100) * $progressWidth);
        $emptyBars = $progressWidth - $filledBars;
        $progressBar = "[" . str_repeat("=", $filledBars) . ">" . str_repeat("-", $emptyBars) . "]";

        if ($current === 1) {
            $this->output->write("\033[?25l");
        }

        if ($current === $total) {
            $progressText = " $current/$total $progressBar " . intval($progress) . "% \033[36mBerhasil\033[0m";
        } else {
            $progressText = " $current/$total $progressBar " . intval($progress) . "% \033[33mMembuat resource $model...\033[0m";
        }

        $this->output->write("\033[2K\r" . str_pad($progressText, 100));

        if ($current === $total) {
            $this->output->write("\033[?25h");
        }

        usleep(1000000);
    }
}

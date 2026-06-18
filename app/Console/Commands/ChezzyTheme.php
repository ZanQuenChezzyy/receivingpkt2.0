<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('chezzy:theme {theme? : default|neobrutalism}')]
#[Description('Switch Chezzy Filament theme')]
class ChezzyTheme extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $theme = $this->argument('theme');

        if (!$theme) {
            $theme = $this->chooseTheme();
        }

        if (!in_array($theme, ['default', 'neobrutalism'])) {
            $this->error('Theme tidak valid');
            return;
        }

        $this->updateConfig($theme);
        $this->updateThemeCss($theme);

        $this->info(" Theme berhasil diubah ke: {$theme}");
        $this->line(' Jalankan: npm run build');
    }

    private function chooseTheme(): string
    {
        $this->line('');
        $this->line(' Pilih tema LaravelChezzy:');
        $this->line(' [1] Default (Laravel Chezzy)');
        $this->line(' [2] Neobrutalism');
        $this->line('');

        $choice = $this->ask('Masukkan nomor tema [1-2]', '1');

        return match ($choice) {
            '1' => 'default',
            '2' => 'neobrutalism',
            default => 'default',
        };
    }


    private function updateConfig(string $theme): void
    {
        $path = config_path('chezzy.php');
        $config = file_get_contents($path);

        $config = preg_replace(
            "/'active_theme'\s*=>\s*'.*?'/",
            "'active_theme' => '{$theme}'",
            $config
        );

        file_put_contents($path, $config);

        // --- TAMBAHAN WAJIB ---
        // 1. Update config di memori/runtime saat ini juga
        config(['chezzy.active_theme' => $theme]);

        // 2. Bersihkan cache config Laravel agar request selanjutnya membaca file terbaru
        $this->callSilent('config:clear');
    }

    private function updateThemeCss(string $theme): void
    {
        $path = resource_path('css/filament/admin/theme.css');

        $css = match ($theme) {
            'neobrutalism' => <<<CSS
@import "../../../../vendor/filament/filament/resources/css/theme.css";

@source '../../../../app/Filament/**/*';
@source '../../../../resources/views/chezzy/**/*';
@source '../../../../resources/views/filament/**/*';
@source '../../../../vendor/devonab/filament-easy-footer/resources/views/**/*';
CSS,

            default => <<<CSS
@import "../../../../vendor/filament/filament/resources/css/theme.css";

@source '../../../../app/Filament/**/*';
@source '../../../../resources/views/chezzy/**/*';
@source '../../../../resources/views/filament/**/*';
@source '../../../../vendor/devonab/filament-easy-footer/resources/views/**/*';
CSS,
        };

        file_put_contents($path, $css);
    }
}

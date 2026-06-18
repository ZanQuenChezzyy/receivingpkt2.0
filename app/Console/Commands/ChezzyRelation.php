<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('chezzy:relation')]
#[Description('Generate Eloquent relationships between models')]
class ChezzyRelation extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $modelsDir = app_path('Models');
        $models = $this->getModels($modelsDir);

        if (empty($models)) {
            $this->abortProcess("Tidak ada model yang ditemukan di direktori $modelsDir.");
        }

        // Tampilkan daftar model
        $this->newLine();
        $this->info(" \033[36mModel yang Ditemukan\033[0m :");
        foreach ($models as $index => $model) {
            $this->line(" [\033[33m$index\033[0m] \033[33m$model\033[0m");
        }

        // Pilih model sumber
        $sourceModel = $this->askForModel("\033[36mMasukkan\033[0m [\033[33mnomor\033[0m] \033[36mmodel\033[0m [\033[33msumber\033[0m]", $models);
        $relations = [];

        $relationTypes = [
            '1' => 'hasOne',
            '2' => 'hasMany',
            '3' => 'hasOneThrough',
            '4' => 'hasManyThrough',
            '5' => 'belongsToMany',
        ];

        do {
            $this->info("\033[36m Tipe Relasi Eloquent\033[0m :");
            foreach ($relationTypes as $key => $type) {
                $this->line(" [\033[33m$key\033[0m] \033[33m$type\033[0m");
            }

            $relationTypeIndex = (int) $this->ask("\033[36mMasukkan\033[0m [\033[33mnomor\033[0m] \033[36m tipe relasi yang diinginkan\033[0m");

            while (!isset($relationTypes[$relationTypeIndex])) {
                $this->info("\033[31m Pilihan tidak valid. Silakan masukkan angka yang benar.\033[0m");
                $relationTypeIndex = (int) $this->ask("\033[36mMasukkan\033[0m [\033[33mnomor\033[0m] \033[36mtipe relasi yang diinginkan\033[0m", '2');
            }

            $relationType = $relationTypes[$relationTypeIndex];

            // Tampilkan daftar model tanpa model sumber yang sudah dipilih
            $filteredModels = array_filter($models, fn($m) => $m !== $sourceModel);
            $filteredModels = array_values($filteredModels);

            $this->info("\033[36mModel yang Ditemukan\033[0m :");
            foreach ($filteredModels as $index => $model) {
                $this->line(" [\033[33m$index\033[0m] \033[33m$model\033[0m");
            }

            $targetModel = $this->askForModel("\033[36mMasukkan\033[0m [\033[33mnomor\033[0m] \033[36mmodel\033[0m [\033[33mtujuan\033[0m]", $filteredModels);

            $intermediateModel = null;
            if (in_array($relationType, ['hasOneThrough', 'hasManyThrough'])) {
                $filteredModels = array_filter($filteredModels, fn($m) => $m !== $targetModel);
                $filteredModels = array_values($filteredModels);

                $this->info("\033[36m Model yang Ditemukan\033[0m :");
                foreach ($filteredModels as $index => $model) {
                    $this->line(" [\033[33m$index\033[0m] \033[33m$model\033[0m");
                }

                $intermediateModel = $this->askForModel("\033[36mMasukkan\033[0m [\033[33mnomor\033[0m] \033[36mmodel\033[0m [\033[33mperantara\033[0m]", $filteredModels);
            }

            $pivotTable = null;
            $foreignPivotKey = null;
            $relatedPivotKey = null;

            if ($relationType === 'belongsToMany') {
                $filteredModels = array_filter($filteredModels, fn($m) => $m !== $targetModel);
                $filteredModels = array_values($filteredModels);
                $this->info("\033[36mModel yang Ditemukan untuk Tabel Pivot\033[0m :");
                foreach ($filteredModels as $index => $model) {
                    $this->line(" [\033[33m$index\033[0m] \033[33m$model\033[0m");
                }

                $pivotModel = $this->askForModel("\033[36mMasukkan\033[0m [\033[33mnomor\033[0m] \033[36mmodel\033[0m [\033[33mpivot\033[0m]", $filteredModels);

                $pivotTable = Str::snake($pivotModel);
                $foreignPivotKey = Str::snake($sourceModel) . '_id';
                $relatedPivotKey = Str::snake($targetModel) . '_id';
            }

            $relations[] = [
                'source' => $sourceModel,
                'target' => $targetModel,
                'type' => $relationType,
                'intermediate' => $intermediateModel,
                'pivotTable' => $pivotTable,
                'foreignPivotKey' => $foreignPivotKey,
                'relatedPivotKey' => $relatedPivotKey,
            ];

        } while ($this->confirm("\033[36mApakah ada lagi?\033[0m", false));

        foreach ($relations as $relation) {
            $this->addRelation($modelsDir, $relation);
        }
    }

    /**
     * Mengambil daftar nama file model dari direktori.
     */
    private function getModels(string $modelsDir): array
    {
        return collect(File::files($modelsDir))
            ->map(fn($file) => pathinfo($file, PATHINFO_FILENAME))
            ->values()
            ->all();
    }

    /**
     * Meminta user memilih model dari daftar array.
     */
    private function askForModel(string $message, array $models, array $exclude = []): string
    {
        do {
            $index = (int) $this->ask($message);
            $model = $models[$index] ?? null;
        } while (!$model || in_array($model, $exclude));

        return $model;
    }

    /**
     * Menghentikan proses secara paksa.
     */
    private function abortProcess(string $message): never
    {
        $this->error("\n$message\n");
        exit(0);
    }

    /**
     * Mengelola penambahan method relasi ke dalam file model.
     */
    private function addRelation(string $modelsDir, array $relation): void
    {
        $sourceFile = "$modelsDir/{$relation['source']}.php";
        $targetFile = "$modelsDir/{$relation['target']}.php";
        $relationMethodSource = $this->createRelationMethod($relation);

        if (file_exists($sourceFile)) {
            $this->appendRelation($sourceFile, $relationMethodSource, $relation['source'], $relation['target']);
        }

        if (file_exists($targetFile)) {
            $belongsToRelation = [
                'source' => $relation['target'],
                'target' => $relation['source'],
                'type' => 'belongsTo',
            ];
            $belongsToMethod = $this->createRelationMethod($belongsToRelation);
            $this->appendRelation($targetFile, $belongsToMethod, $relation['target'], $relation['source']);
        }
    }

    /**
     * Membuat string method relasi berdasarkan template stub.
     */
    private function createRelationMethod(array $relation): string
    {
        $relationType = $relation['type'];
        $targetModel = $relation['target'];
        $intermediateModel = $relation['intermediate'] ?? null;
        $pivotTable = $relation['pivotTable'] ?? null;
        $foreignPivotKey = $relation['foreignPivotKey'] ?? null;
        $relatedPivotKey = $relation['relatedPivotKey'] ?? null;

        $stubPath = app_path("Console/Commands/Template/Relation/{$relationType}.stub");

        if (!File::exists($stubPath)) {
            $this->abortProcess("file tidak ditemukan: $stubPath");
        }

        $stub = File::get($stubPath);
        $methodName = Str::camel(Str::plural($targetModel));

        // Pada belongsTo, nama method biasanya singular (bukan plural)
        if ($relationType === 'belongsTo' || $relationType === 'hasOne') {
            $methodName = Str::camel($targetModel);
        }

        $relatedClass = "\\App\\Models\\$targetModel::class";
        $intermediateClass = $intermediateModel ? "\\App\\Models\\$intermediateModel::class" : '';
        $foreignKey = Str::snake($targetModel) . "_id";

        // Memperbaiki penentuan foreign key untuk belongsTo
        if ($relationType === 'belongsTo') {
            $foreignKey = Str::snake($relation['target'] ?? $targetModel) . "_id";
        }

        $extraParams = '';

        switch ($relationType) {
            case 'hasOneThrough':
            case 'hasManyThrough':
                if (!$intermediateModel) {
                    $this->abortProcess("Tipe relasi $relationType membutuhkan model perantara.");
                }
                $extraParams = ", $intermediateClass";
                break;
        }

        if ($relationType === 'belongsToMany') {
            return str_replace(
                ['{{methodName}}', '{{relatedClass}}', '{{pivotTable}}', '{{foreignPivotKey}}', '{{relatedPivotKey}}'],
                [$methodName, $relatedClass, $pivotTable, $foreignPivotKey, $relatedPivotKey],
                $stub
            );
        }

        return str_replace(
            ['{{methodName}}', '{{relationType}}', '{{relatedClass}}', '{{foreignKey}}', '{{extraParams}}'],
            [$methodName, $relationType, $relatedClass, $foreignKey, $extraParams],
            $stub
        );
    }

    /**
     * Menyisipkan string method ke dalam file model.
     */
    private function appendRelation(string $filePath, string $relationMethod, string $source, string $target): void
    {
        $fileContent = file_get_contents($filePath);
        $methodName = Str::camel(Str::plural($target));

        // Tambahan logika agar pengecekan method singular/plural lebih akurat
        $singularMethodName = Str::camel($target);

        if (!str_contains($fileContent, "function $methodName()") && !str_contains($fileContent, "function $singularMethodName()")) {
            $fileContent = preg_replace('/}\s*$/', "\n    $relationMethod\n}", $fileContent);
            file_put_contents($filePath, $fileContent);
            $this->info(" Relasi dari $source ke $target berhasil ditambahkan.");
        } else {
            $this->warn(" Relasi sudah ada di model $source.");
        }
    }
}

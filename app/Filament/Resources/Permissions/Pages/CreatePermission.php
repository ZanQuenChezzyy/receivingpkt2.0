<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $baseName = $data['name'];

        // Jika tidak mengandung action keyword
        if (! preg_match('/\b(View|Create|Update|Delete|Restore|Force Delete|View Any)\b/i', $baseName)) {
            $actions = ['View Any', 'View', 'Create', 'Update', 'Delete', 'Restore', 'Force Delete'];

            $firstModel = null;

            foreach ($actions as $action) {
                $permission = static::getModel()::firstOrCreate([
                    'name' => "{$action} {$baseName}",
                    'guard_name' => $data['guard_name'] ?? 'web', // Spatie membutuhkan guard_name
                ]);

                // Simpan model pertama untuk dikembalikan ke Filament (mencegah error missing record)
                if (! $firstModel) {
                    $firstModel = $permission;
                }
            }

            return $firstModel;
        }

        // Jika mengandung action keyword, simpan normal
        return static::getModel()::create($data);
    }

    /**
     * Redirect kembali ke halaman Index setelah generate selesai
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

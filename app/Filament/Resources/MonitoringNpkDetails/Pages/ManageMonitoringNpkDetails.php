<?php

namespace App\Filament\Resources\MonitoringNpkDetails\Pages;

use App\Filament\Resources\MonitoringNpkDetails\MonitoringNpkDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageMonitoringNpkDetails extends ManageRecords
{
    protected static string $resource = MonitoringNpkDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Detail NPK')
                ->icon(Heroicon::PlusCircle),
        ];
    }
}

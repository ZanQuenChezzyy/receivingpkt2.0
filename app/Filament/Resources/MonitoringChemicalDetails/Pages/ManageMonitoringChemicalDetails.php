<?php

namespace App\Filament\Resources\MonitoringChemicalDetails\Pages;

use App\Filament\Resources\MonitoringChemicalDetails\MonitoringChemicalDetailResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMonitoringChemicalDetails extends ManageRecords
{
    protected static string $resource = MonitoringChemicalDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\MonitoringChemicals\Pages;

use App\Filament\Resources\MonitoringChemicals\MonitoringChemicalResource;
use App\Services\SyncChemicalToDeliveryOrderService;
use Filament\Resources\Pages\CreateRecord;

class CreateMonitoringChemical extends CreateRecord
{
    protected static string $resource = MonitoringChemicalResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Sync ke Delivery Order Receipt
        app(SyncChemicalToDeliveryOrderService::class)->sync($record);
    }
}

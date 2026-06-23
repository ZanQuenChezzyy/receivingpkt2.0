<?php

namespace App\Filament\Resources\MonitoringChemicals\Pages;

use App\Filament\Resources\MonitoringChemicals\MonitoringChemicalResource;
use App\Services\SyncChemicalToDeliveryOrderService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMonitoringChemical extends EditRecord
{
    protected static string $resource = MonitoringChemicalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        // Sync ke Delivery Order Receipt
        app(SyncChemicalToDeliveryOrderService::class)->sync($record);
    }
}

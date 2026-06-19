<?php

namespace App\Filament\Resources\MonitoringNpks\Pages;

use App\Filament\Resources\MonitoringNpks\MonitoringNpkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMonitoringNpk extends CreateRecord
{
    protected static string $resource = MonitoringNpkResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $exists = \App\Models\MonitoringNpk::where('purchase_order_terbit_id', $data['purchase_order_terbit_id'])
            ->where('delivery_oder_number', $data['delivery_oder_number'])
            ->exists();

        if ($exists) {
            \Filament\Notifications\Notification::make()
                ->title('Duplikat DO')
                ->body('Kombinasi Nomor PO dan Nomor DO ini sudah ada di sistem.')
                ->danger()
                ->send();
            
            $this->halt();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        if ($record->isDone()) {
            $record->update(['doc_status' => 'Completed']);
            app(\App\Services\SyncNpkToDeliveryOrderService::class)->sync($record);
        } else {
            $record->update(['doc_status' => 'Outstanding']);
        }
    }
}

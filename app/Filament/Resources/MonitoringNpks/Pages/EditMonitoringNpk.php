<?php

namespace App\Filament\Resources\MonitoringNpks\Pages;

use App\Filament\Resources\MonitoringNpks\MonitoringNpkResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMonitoringNpk extends EditRecord
{
    protected static string $resource = MonitoringNpkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->record;
        
        $exists = \App\Models\MonitoringNpk::where('purchase_order_terbit_id', $data['purchase_order_terbit_id'])
            ->where('delivery_oder_number', $data['delivery_oder_number'])
            ->where('id', '!=', $record->id)
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

    protected function afterSave(): void
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

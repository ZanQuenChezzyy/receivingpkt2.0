<?php

namespace App\Filament\Resources\DeliveryOrderReceiptDetails\Pages;

use App\Filament\Resources\DeliveryOrderReceiptDetails\DeliveryOrderReceiptDetailResource;
// use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDeliveryOrderReceiptDetails extends ManageRecords
{
    protected static string $resource = DeliveryOrderReceiptDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}

<?php

namespace App\Filament\Resources\LocationReceivings\Pages;

use App\Filament\Resources\LocationReceivings\LocationReceivingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLocationReceivings extends ManageRecords
{
    protected static string $resource = LocationReceivingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

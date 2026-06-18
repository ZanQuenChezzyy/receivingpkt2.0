<?php

namespace App\Filament\Resources\Transmittals\Pages;

use App\Filament\Resources\Transmittals\TransmittalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransmittal extends ViewRecord
{
    protected static string $resource = TransmittalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

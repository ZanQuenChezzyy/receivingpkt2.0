<?php

namespace App\Filament\Resources\Transmittals\Pages;

use App\Filament\Resources\Transmittals\TransmittalResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTransmittals extends ListRecords
{
    protected static string $resource = TransmittalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulk_scan')
                ->label('Bulk Scan Transmittal')
                ->icon('heroicon-o-qr-code')
                ->color('primary')
                ->url(fn (): string => TransmittalResource::getUrl('bulk-scan')),
        ];
    }
}

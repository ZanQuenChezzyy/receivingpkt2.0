<?php

namespace App\Filament\Resources\GrsRdtvs\Pages;

use App\Filament\Resources\GrsRdtvs\GrsRdtvResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGrsRdtvs extends ListRecords
{
    protected static string $resource = GrsRdtvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

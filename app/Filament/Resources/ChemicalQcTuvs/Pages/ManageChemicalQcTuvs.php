<?php

namespace App\Filament\Resources\ChemicalQcTuvs\Pages;

use App\Filament\Resources\ChemicalQcTuvs\ChemicalQcTuvResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageChemicalQcTuvs extends ManageRecords
{
    protected static string $resource = ChemicalQcTuvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah QC TUV')
                ->icon(Heroicon::PlusCircle),
        ];
    }
}

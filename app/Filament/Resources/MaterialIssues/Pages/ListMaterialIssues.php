<?php

namespace App\Filament\Resources\MaterialIssues\Pages;

use App\Filament\Resources\MaterialIssues\MaterialIssueResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMaterialIssues extends ListRecords
{
    protected static string $resource = MaterialIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

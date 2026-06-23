<?php

namespace App\Filament\Resources\MaterialIssues\Pages;

use App\Filament\Resources\MaterialIssues\MaterialIssueResource;
use App\Models\MaterialIssue;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMaterialIssue extends EditRecord
{
    protected static string $resource = MaterialIssueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_mir')
                ->label('Cetak MIR')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (MaterialIssue $record): string => route('filament.admin.resources.material-issues.print', $record))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}

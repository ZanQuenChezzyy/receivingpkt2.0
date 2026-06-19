<?php

namespace App\Filament\Resources\MonitoringNpks\Pages;

use App\Filament\Resources\MonitoringNpks\MonitoringNpkResource;
use App\Models\MonitoringNpk;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListMonitoringNpks extends ListRecords
{
    protected static string $resource = MonitoringNpkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make(),
            'Outstanding' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('doc_status', 'Outstanding'))
                ->badge(MonitoringNpk::where('doc_status', 'Outstanding')->count())
                ->badgeColor('warning'),
            'Completed' => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('doc_status', 'Completed'))
                ->badge(MonitoringNpk::where('doc_status', 'Completed')->count())
                ->badgeColor('success'),
        ];
    }
}

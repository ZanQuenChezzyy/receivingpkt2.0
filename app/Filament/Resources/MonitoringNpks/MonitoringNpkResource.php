<?php

namespace App\Filament\Resources\MonitoringNpks;

use App\Filament\Resources\MonitoringNpks\Pages\CreateMonitoringNpk;
use App\Filament\Resources\MonitoringNpks\Pages\EditMonitoringNpk;
use App\Filament\Resources\MonitoringNpks\Pages\ListMonitoringNpks;
use App\Filament\Resources\MonitoringNpks\Pages\ViewMonitoringNpk;
use App\Filament\Resources\MonitoringNpks\Schemas\MonitoringNpkForm;
use App\Filament\Resources\MonitoringNpks\Schemas\MonitoringNpkInfolist;
use App\Filament\Resources\MonitoringNpks\Tables\MonitoringNpksTable;
use App\Models\MonitoringNpk;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class MonitoringNpkResource extends Resource
{
    protected static ?string $model = MonitoringNpk::class;

    protected static string|UnitEnum|null $navigationGroup = 'Penerimaan Receiving';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Sparkles;

    public static function getNavigationLabel(): string
    {
        return 'Monitoring NPK';
    }

    public static function getModelLabel(): string
    {
        return 'Monitoring NPK';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Monitoring NPK';
    }

    public static function form(Schema $schema): Schema
    {
        return MonitoringNpkForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MonitoringNpkInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MonitoringNpksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMonitoringNpks::route('/'),
            'create' => CreateMonitoringNpk::route('/create'),
            'view' => ViewMonitoringNpk::route('/{record}'),
            'edit' => EditMonitoringNpk::route('/{record}/edit'),
        ];
    }
}

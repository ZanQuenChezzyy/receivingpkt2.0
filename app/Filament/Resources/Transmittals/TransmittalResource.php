<?php

namespace App\Filament\Resources\Transmittals;

use App\Filament\Resources\Transmittals\Pages\BulkScanTransmittal;
use App\Filament\Resources\Transmittals\Pages\CreateTransmittal;
use App\Filament\Resources\Transmittals\Pages\EditTransmittal;
use App\Filament\Resources\Transmittals\Pages\ListTransmittals;
use App\Filament\Resources\Transmittals\Pages\ViewTransmittal;
use App\Filament\Resources\Transmittals\Schemas\TransmittalForm;
use App\Filament\Resources\Transmittals\Schemas\TransmittalInfolist;
use App\Filament\Resources\Transmittals\Tables\TransmittalsTable;
use App\Models\Transmittal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class TransmittalResource extends Resource
{
    protected static ?string $model = Transmittal::class;

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::PaperAirplane;

    public static function getNavigationLabel(): string
    {
        return 'Pengajuan QC';
    }

    public static function getModelLabel(): string
    {
        return 'Pengajuan QC';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengajuan QC';
    }

    public static function form(Schema $schema): Schema
    {
        return TransmittalForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TransmittalInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransmittalsTable::configure($table);
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
            'index' => ListTransmittals::route('/'),
            'create' => CreateTransmittal::route('/create'),
            'bulk-scan' => BulkScanTransmittal::route('/bulk-scan'),
            'view' => ViewTransmittal::route('/{record}'),
            'edit' => EditTransmittal::route('/{record}/edit'),
        ];
    }
}

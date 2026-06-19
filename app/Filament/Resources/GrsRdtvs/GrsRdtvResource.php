<?php

namespace App\Filament\Resources\GrsRdtvs;

use App\Filament\Resources\GrsRdtvs\Pages\CreateGrsRdtv;
use App\Filament\Resources\GrsRdtvs\Pages\EditGrsRdtv;
use App\Filament\Resources\GrsRdtvs\Pages\ListGrsRdtvs;
use App\Filament\Resources\GrsRdtvs\Pages\ViewGrsRdtv;
use App\Filament\Resources\GrsRdtvs\RelationManagers\GrsRdtvItemsRelationManager;
use App\Filament\Resources\GrsRdtvs\Schemas\GrsRdtvForm;
use App\Filament\Resources\GrsRdtvs\Schemas\GrsRdtvInfolist;
use App\Filament\Resources\GrsRdtvs\Tables\GrsRdtvsTable;
use App\Models\GrsRdtv;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class GrsRdtvResource extends Resource
{
    protected static ?string $model = GrsRdtv::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'heroicon-s-document-currency-dollar';

    public static function getNavigationLabel(): string
    {
        return 'GRS & RDTV';
    }

    public static function getModelLabel(): string
    {
        return 'GRS & RDTV';
    }

    public static function getPluralModelLabel(): string
    {
        return 'GRS & RDTV';
    }

    public static function form(Schema $schema): Schema
    {
        return GrsRdtvForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GrsRdtvInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GrsRdtvsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            GrsRdtvItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGrsRdtvs::route('/'),
            'create' => CreateGrsRdtv::route('/create'),
            'view' => ViewGrsRdtv::route('/{record}'),
            'edit' => EditGrsRdtv::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\MaterialIssues;

use App\Filament\Resources\MaterialIssues\Pages\CreateMaterialIssue;
use App\Filament\Resources\MaterialIssues\Pages\EditMaterialIssue;
use App\Filament\Resources\MaterialIssues\Pages\ListMaterialIssues;
use App\Filament\Resources\MaterialIssues\Schemas\MaterialIssueForm;
use App\Filament\Resources\MaterialIssues\Schemas\MaterialIssueInfolist;
use App\Filament\Resources\MaterialIssues\Tables\MaterialIssuesTable;
use App\Models\MaterialIssue;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MaterialIssueResource extends Resource
{
    protected static ?string $model = MaterialIssue::class;

    protected static string|UnitEnum|null $navigationGroup = 'Pengeluaran Material';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowUp;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::DocumentArrowUp;

    public static function getNavigationLabel(): string
    {
        return 'Material Issue (MIR)';
    }

    public static function getModelLabel(): string
    {
        return 'Material Issue (MIR)';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Material Issue (MIR)';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return MaterialIssueForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MaterialIssueInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaterialIssuesTable::configure($table);
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
            'index' => ListMaterialIssues::route('/'),
            'create' => CreateMaterialIssue::route('/create'),
            'edit' => EditMaterialIssue::route('/{record}/edit'),
        ];
    }
}

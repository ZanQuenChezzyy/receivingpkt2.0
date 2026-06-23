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

class MaterialIssueResource extends Resource
{
    protected static ?string $model = MaterialIssue::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

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

<?php

namespace App\Filament\Resources\ChemicalQcTuvs;

use App\Filament\Resources\ChemicalQcTuvs\Pages\ManageChemicalQcTuvs;
use App\Models\ChemicalQcTuv;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChemicalQcTuvResource extends Resource
{
    protected static ?string $model = ChemicalQcTuv::class;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::ShieldCheck;

    public static function getNavigationLabel(): string
    {
        return 'QC TUV';
    }

    public static function getModelLabel(): string
    {
        return 'QC TUV';
    }

    public static function getPluralModelLabel(): string
    {
        return 'QC TUV';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('purchase_order_issued_id')
                    ->relationship('purchaseOrderIssued', 'id')
                    ->required(),
                TextInput::make('tahapan_name')
                    ->required(),
                TextInput::make('qty_qc_tuv')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('purchaseOrderIssued.id')
                    ->label('Purchase order issued'),
                TextEntry::make('tahapan_name'),
                TextEntry::make('qty_qc_tuv')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purchaseOrderIssued.id')
                    ->searchable(),
                TextColumn::make('tahapan_name')
                    ->searchable(),
                TextColumn::make('qty_qc_tuv')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageChemicalQcTuvs::route('/'),
        ];
    }
}

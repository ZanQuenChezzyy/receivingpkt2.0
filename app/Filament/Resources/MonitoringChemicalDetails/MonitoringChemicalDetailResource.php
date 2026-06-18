<?php

namespace App\Filament\Resources\MonitoringChemicalDetails;

use App\Filament\Resources\MonitoringChemicalDetails\Pages\ManageMonitoringChemicalDetails;
use App\Models\MonitoringChemicalDetail;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MonitoringChemicalDetailResource extends Resource
{
    protected static ?string $model = MonitoringChemicalDetail::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('monitoring_chemical_id')
                    ->required()
                    ->numeric(),
                TextInput::make('chemical_qc_tuv_id')
                    ->numeric(),
                TextInput::make('quantity_received')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('monitoring_chemical_id')
                    ->numeric(),
                TextEntry::make('chemical_qc_tuv_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('quantity_received')
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
                TextColumn::make('monitoring_chemical_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('chemical_qc_tuv_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity_received')
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
            'index' => ManageMonitoringChemicalDetails::route('/'),
        ];
    }
}

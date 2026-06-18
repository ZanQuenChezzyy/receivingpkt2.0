<?php

namespace App\Filament\Resources\MonitoringNpkDetails;

use App\Filament\Resources\MonitoringNpkDetails\Pages\ManageMonitoringNpkDetails;
use App\Models\MonitoringNpkDetail;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MonitoringNpkDetailResource extends Resource
{
    protected static ?string $model = MonitoringNpkDetail::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('monitoring_npk_id')
                    ->relationship('monitoringNpk', 'id')
                    ->required(),
                TextInput::make('item_no')
                    ->required()
                    ->numeric(),
                TextInput::make('material_code')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('uoi')
                    ->required(),
                Select::make('location_id')
                    ->relationship('location', 'name'),
                Toggle::make('is_qty_tolerance')
                    ->required(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('monitoringNpk.id')
                    ->label('Monitoring npk'),
                TextEntry::make('item_no')
                    ->numeric(),
                TextEntry::make('material_code'),
                TextEntry::make('description')
                    ->columnSpanFull(),
                TextEntry::make('quantity')
                    ->numeric(),
                TextEntry::make('uoi'),
                TextEntry::make('location.name')
                    ->label('Location')
                    ->placeholder('-'),
                IconEntry::make('is_qty_tolerance')
                    ->boolean(),
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
                TextColumn::make('monitoringNpk.id')
                    ->searchable(),
                TextColumn::make('item_no')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('material_code')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('uoi')
                    ->searchable(),
                TextColumn::make('location.name')
                    ->searchable(),
                IconColumn::make('is_qty_tolerance')
                    ->boolean(),
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
            'index' => ManageMonitoringNpkDetails::route('/'),
        ];
    }
}

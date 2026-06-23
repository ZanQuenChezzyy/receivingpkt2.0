<?php

namespace App\Filament\Resources\MonitoringChemicals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MonitoringChemicalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('material_category')
                    ->searchable(),
                TextColumn::make('qc_by')
                    ->searchable(),
                TextColumn::make('do_number')
                    ->searchable(),
                TextColumn::make('receivedBy.name')
                    ->label('Penerima')
                    ->searchable(),
                TextColumn::make('received_date')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('doc_status')
                    ->searchable(),
                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

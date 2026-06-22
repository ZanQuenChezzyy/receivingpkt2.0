<?php

namespace App\Filament\Resources\DeliveryOrderReceipts\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransmittalsRelationManager extends RelationManager
{
    protected static string $relationship = 'transmittals';

    protected static ?string $title = 'Data Transmittal';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('transmittal_no')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('transmittal_no')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal & Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('transmittals.created_at', $direction)),

                TextColumn::make('transmittal_no')
                    ->label('Nomor Transmittal')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary')
                    ->url(fn ($record) => route('filament.admin.resources.transmittals.view', $record)),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => $state === 'Kirim' ? 'primary' : 'warning'),

                TextColumn::make('destination')
                    ->label('Tujuan')
                    ->badge()
                    ->color(fn ($state) => $state === 'ISTEK' ? 'info' : 'success'),

                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->sortable(),
            ])
            ->defaultSort('transmittals.created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Keluarkan dari Transmittal'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}

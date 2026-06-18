<?php

namespace App\Filament\Resources\Transmittals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransmittalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transmittal_no')
                    ->label('Nomor Transmittal')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nomor disalin'),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn ($state) => $state === 'Kirim' ? 'primary' : 'warning'),

                TextColumn::make('destination')
                    ->label('Tujuan')
                    ->badge()
                    ->color(fn ($state) => $state === 'ISTEK' ? 'info' : 'success'),

                TextColumn::make('total_documents')
                    ->getStateUsing(fn ($record) => $record->transmittalItems()->count())
                    ->label('Total Dokumen')
                    ->suffix(' Dokumen')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal & Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Transmittal')
                    ->options([
                        'Kirim' => 'Kirim',
                        'Kembali' => 'Kembali',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('destination')
                    ->label('Tujuan')
                    ->options([
                        'ISTEK' => 'ISTEK',
                        'PPE' => 'PPE',
                    ]),
            ])
            ->recordUrl(null)
            ->recordAction(ViewAction::class)
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->url(fn (\App\Models\Transmittal $record): string => \App\Filament\Resources\Transmittals\TransmittalResource::getUrl('bulk-scan', ['id' => $record->id])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

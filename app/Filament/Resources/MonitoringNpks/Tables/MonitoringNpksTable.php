<?php

namespace App\Filament\Resources\MonitoringNpks\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MonitoringNpksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('purchaseOrderIssued.purchase_order_no')
                    ->label('Nomor PO')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('delivery_oder_number')
                    ->label('Nomor DO')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('sample_receivied_date')
                    ->label('Sample')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : 'Belum Ada'),

                IconColumn::make('delivery_oder_delivery_date')
                    ->label('DO Dikirim')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : 'Belum Ada'),

                IconColumn::make('received_date')
                    ->label('Penerimaan')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : 'Belum Ada'),

                IconColumn::make('purchase_order_103_date')
                    ->label('103')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : 'Belum Ada'),

                IconColumn::make('laprima_date')
                    ->label('LAPRIMA')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : 'Belum Ada'),

                IconColumn::make('coa_date')
                    ->label('COA')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn ($state) => $state ? Carbon::parse($state)->format('d/m/Y') : 'Belum Ada'),

                IconColumn::make('purchase_order_status')
                    ->label('Status PO')
                    ->icon(fn ($record) => ($record->purchase_order_status === 'A' && is_array($record->purchase_order_status_a_files) && count($record->purchase_order_status_a_files) > 0) ||
                        ($record->purchase_order_status === 'B' && filled($record->purchase_order_status_b_date))
                        ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'
                    )
                    ->color(fn ($record) => ($record->purchase_order_status === 'A' && is_array($record->purchase_order_status_a_files) && count($record->purchase_order_status_a_files) > 0) ||
                        ($record->purchase_order_status === 'B' && filled($record->purchase_order_status_b_date))
                        ? 'success' : 'gray'
                    )
                    ->tooltip(fn ($record) => $record->purchase_order_status ?: 'Belum Ada'),

                TextColumn::make('doc_status')
                    ->label('Status Dok.')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Completed' => 'success',
                        'Outstanding' => 'warning',
                        default => 'gray',
                    }),

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

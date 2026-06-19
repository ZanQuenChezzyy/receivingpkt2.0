<?php

namespace App\Filament\Resources\GrsRdtvs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class GrsRdtvsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColumnGroup::make('Informasi GRS & RDTV', [
                    TextColumn::make('transaction_date')
                        ->label('Tanggal Eksekusi')
                        ->icon('heroicon-m-calendar-days')
                        ->iconColor('primary')
                        ->color('primary')
                        ->weight(FontWeight::Bold)
                        ->date('d F Y')
                        ->description(fn ($record) => Carbon::parse($record->transaction_date)->translatedFormat('l'))
                        ->sortable(),

                    TextColumn::make('category')
                        ->label('Kategori')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'GRS' => 'success',
                            'RDTV' => 'warning',
                            default => 'gray',
                        })
                        ->icon(fn (string $state): string => match ($state) {
                            'GRS' => 'heroicon-m-document-check',
                            'RDTV' => 'heroicon-m-arrow-path',
                            default => 'heroicon-m-document',
                        }),
                ]),

                ColumnGroup::make('Detail Dokumen', [
                    TextColumn::make('po_numbers')
                        ->label('Nomor PO')
                        ->icon('heroicon-m-document-duplicate')
                        ->iconColor('gray')
                        ->getStateUsing(function ($record) {
                            return $record->grsRdtvItems
                                ->map(fn ($item) => $item->deliveryOrderReceipt?->deliveryOrderReceiptDetails?->first()?->purchaseOrderIssued?->purchase_order_no)
                                ->filter()
                                ->unique()
                                ->toArray();
                        })
                        ->listWithLineBreaks()
                        ->bulleted()
                        ->limitList(2)
                        ->expandableLimitedList()
                        ->searchable(query: function (Builder $query, string $search) {
                            $query->whereHas('grsRdtvItems.deliveryOrderReceipt.deliveryOrderReceiptDetails.purchaseOrderIssued', function ($q) use ($search) {
                                $q->where('purchase_order_no', 'like', "%{$search}%");
                            });
                        }),

                    TextColumn::make('grs_rdtv_items_count')
                        ->label('Total File')
                        ->counts('grsRdtvItems')
                        ->badge()
                        ->suffix(' Dokumen')
                        ->color('info')
                        ->icon('heroicon-m-document-duplicate'),

                    TextColumn::make('createdBy.name')
                        ->label('Eksekutor')
                        ->icon('heroicon-m-user-circle')
                        ->color('gray')
                        ->sortable(),
                ]),

                TextColumn::make('created_at')
                    ->label('Waktu Dibuat')
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

<?php

namespace App\Filament\Resources\GrsRdtvs\RelationManagers;

use App\Filament\Resources\DeliveryOrderReceipts\DeliveryOrderReceiptResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class GrsRdtvItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'grsRdtvItems';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('delivery_order_receipt_id')
                    ->numeric(),
                TextInput::make('document_code')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('Matched'),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('delivery_order_receipt_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('document_code'),
                TextEntry::make('file_path'),
                TextEntry::make('status'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('document_code')
            ->columns([
                TextColumn::make('po_number')
                    ->label('Nomor PO')
                    ->icon('heroicon-m-document-duplicate')
                    ->iconColor('primary')
                    ->color('primary')
                    ->weight(FontWeight::Bold)
                    ->getStateUsing(fn ($record) => $record->deliveryOrderReceipt?->deliveryOrderReceiptDetails?->first()?->purchaseOrderIssued?->purchase_order_no ?? '-')
                    ->url(fn ($record) => $record->delivery_order_receipt_id ? DeliveryOrderReceiptResource::getUrl('view', ['record' => $record->delivery_order_receipt_id]) : null)
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('deliveryOrderReceipt.deliveryOrderReceiptDetails.purchaseOrderIssued', function ($q) use ($search) {
                            $q->where('purchase_order_no', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('deliveryOrderReceipt.delivery_oder_no')
                    ->label('No. DO')
                    ->icon('heroicon-m-document-text')
                    ->iconColor('gray')
                    ->placeholder('-')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('document_code')
                    ->label('Kode Dokumen / Nama File')
                    ->icon('heroicon-m-qr-code')
                    ->copyable()
                    ->copyMessage('Nama file disalin')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Matched' => 'success',
                        'Not Found' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Matched' => 'heroicon-m-check-circle',
                        'Not Found' => 'heroicon-m-x-circle',
                        default => 'heroicon-m-information-circle',
                    })
                    ->searchable(),
                TextColumn::make('file_path')
                    ->label('File')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('info')
                    ->weight(FontWeight::Bold)
                    ->formatStateUsing(fn ($state) => 'Lihat Dokumen')
                    ->url(fn ($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),
                TextColumn::make('created_at')
                    ->label('Waktu Unggah')
                    ->icon('heroicon-m-clock')
                    ->iconColor('gray')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\DeliveryOrderReceipts\Pages;

use App\Filament\Resources\DeliveryOrderReceipts\DeliveryOrderReceiptResource;
use App\Models\DeliveryOrderReceipt;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ListDeliveryOrderReceipts extends ListRecords
{
    protected static string $resource = DeliveryOrderReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Penerimaan DO')
                ->icon(Heroicon::PlusCircle),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make('Semua')
                ->icon('heroicon-o-list-bullet'),
            'Pending Dokumen' => Tab::make('Pending Dokumen')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Pending'))
                ->icon('heroicon-o-clock')
                ->badgeColor('danger')
                ->badge(DeliveryOrderReceipt::where('status', 'Pending')->count()),
            'Menunggu Kedatangan Fisik (Transit)' => Tab::make('Menunggu Kedatangan Fisik (Transit)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_physically_received', false)->where('receipt_mode', '!=', 'Standard'))
                ->icon('heroicon-o-truck')
                ->badgeColor('warning')
                ->badge(DeliveryOrderReceipt::where('is_physically_received', false)->where('receipt_mode', '!=', 'Standard')->count()),
            'Fisik Sudah Tiba' => Tab::make('Fisik Sudah Tiba')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_physically_received', true))
                ->icon('heroicon-o-check-badge'),
        ];
    }
}

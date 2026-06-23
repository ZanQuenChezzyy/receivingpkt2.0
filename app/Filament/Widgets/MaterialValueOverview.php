<?php

namespace App\Filament\Widgets;

use App\Models\DeliveryOrderReceiptDetail;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class MaterialValueOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Daftar 4 MRPType secara spesifik sesuai permintaan:
        $mrpTypes = ['V1', 'PD', 'NONSTOCK', 'INVESTASI'];

        $stats = [];

        $uiConfig = [
            'V1' => ['color' => 'primary'],
            'PD' => ['color' => 'success'],
            'NONSTOCK' => ['color' => 'warning'],
            'INVESTASI' => ['color' => 'danger'],
        ];

        foreach ($mrpTypes as $mrpType) {
            // 1. GRS Selesai Bulan Ini (Untuk Total Utama)
            $baseGrsQuery = DeliveryOrderReceiptDetail::query()
                ->join('purchase_order_issueds', 'delivery_order_receipt_details.purchase_order_issued_id', '=', 'purchase_order_issueds.id')
                ->where('purchase_order_issueds.mrp_type', $mrpType)
                ->whereHas('deliveryOrderReceipt.grsRdtvItems.grsRdtv', function ($query) {
                    $query->whereMonth('transaction_date', now()->month)
                        ->whereYear('transaction_date', now()->year);
                });

            $totalGrsBulanIni = (clone $baseGrsQuery)->sum('delivery_order_receipt_details.total_amount_snapshot');

            // 2. Kedatangan Murni (Hanya melihat fisik tiba bulan ini, GRS atau belum tidak peduli)
            $kedatanganMurni = DeliveryOrderReceiptDetail::query()
                ->join('purchase_order_issueds', 'delivery_order_receipt_details.purchase_order_issued_id', '=', 'purchase_order_issueds.id')
                ->where('purchase_order_issueds.mrp_type', $mrpType)
                ->whereHas('deliveryOrderReceipt', function ($query) {
                    $query->whereMonth('received_date', now()->month)
                        ->whereYear('received_date', now()->year);
                })
                ->sum('delivery_order_receipt_details.total_amount_snapshot');

            // 3. Datang & Selesai GRS (Datang bulan ini DAN selesai di-GRS bulan ini)
            $kedatanganDanGrs = DeliveryOrderReceiptDetail::query()
                ->join('purchase_order_issueds', 'delivery_order_receipt_details.purchase_order_issued_id', '=', 'purchase_order_issueds.id')
                ->where('purchase_order_issueds.mrp_type', $mrpType)
                ->whereHas('deliveryOrderReceipt', function ($query) {
                    $query->whereMonth('received_date', now()->month)
                        ->whereYear('received_date', now()->year);
                })
                ->whereHas('deliveryOrderReceipt.grsRdtvItems.grsRdtv', function ($query) {
                    $query->whereMonth('transaction_date', now()->month)
                        ->whereYear('transaction_date', now()->year);
                })
                ->sum('delivery_order_receipt_details.total_amount_snapshot');

            // 4. Belum GRS (Total seluruh antrean dari kapanpun yang belum di-GRS)
            $belumGrs = DeliveryOrderReceiptDetail::query()
                ->join('purchase_order_issueds', 'delivery_order_receipt_details.purchase_order_issued_id', '=', 'purchase_order_issueds.id')
                ->where('purchase_order_issueds.mrp_type', $mrpType)
                ->whereDoesntHave('deliveryOrderReceipt.grsRdtvItems')
                ->sum('delivery_order_receipt_details.total_amount_snapshot');

            // 5. Hitung total berdasarkan masing-masing ABC Indicator dari GRS Bulan Ini
            $abcIndicators = (clone $baseGrsQuery)
                ->select('purchase_order_issueds.abc_indicator', DB::raw('SUM(delivery_order_receipt_details.total_amount_snapshot) as total'))
                ->whereNotNull('purchase_order_issueds.abc_indicator')
                ->groupBy('purchase_order_issueds.abc_indicator')
                ->orderByDesc('total')
                ->get();

            // Susun tampilan deskripsi menggunakan HTML String list sederhana dengan Icon SVG
            $descriptionHtml = '<div class="w-full block text-sm mt-2 space-y-1.5">';

            // Baris: Kedatangan Murni (Warna Biru Netral)
            $descriptionHtml .= '<div class="flex items-start justify-between gap-2 font-medium text-blue-600 dark:text-blue-400">';
            $descriptionHtml .= '  <span class="flex items-center gap-1.5 leading-tight"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg> Kedatangan</span>';
            $descriptionHtml .= '  <span class="whitespace-nowrap ml-auto text-right">Rp'.number_format($kedatanganMurni, 0, ',', '.').'</span>';
            $descriptionHtml .= '</div>';

            // Baris: Datang & Selesai GRS (Warna Hijau)
            $descriptionHtml .= '<div class="flex items-start justify-between gap-2 font-medium text-success-600 dark:text-success-400">';
            $descriptionHtml .= '  <span class="flex items-center gap-1.5 leading-tight"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Selesai</span>';
            $descriptionHtml .= '  <span class="whitespace-nowrap ml-auto text-right">Rp'.number_format($kedatanganDanGrs, 0, ',', '.').'</span>';
            $descriptionHtml .= '</div>';

            // Baris: Belum GRS (Warna Merah)
            $descriptionHtml .= '<div class="flex items-start justify-between gap-2 font-medium text-danger-600 dark:text-danger-400">';
            $descriptionHtml .= '  <span class="flex items-center gap-1.5 leading-tight"><svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Belum GRS</span>';
            $descriptionHtml .= '  <span class="whitespace-nowrap ml-auto text-right">Rp'.number_format($belumGrs, 0, ',', '.').'</span>';
            $descriptionHtml .= '</div>';

            if ($abcIndicators->isNotEmpty()) {
                $descriptionHtml .= '<div class="my-2 border-t border-gray-200 dark:border-gray-700"></div>';
                foreach ($abcIndicators as $abc) {
                    $descriptionHtml .= '<div class="flex items-start justify-between gap-2 text-gray-600 dark:text-gray-300">';
                    $descriptionHtml .= '  <span class="flex items-center gap-1.5 leading-tight"><svg class="w-2.5 h-2.5 shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"></circle></svg> Tipe '.$abc->abc_indicator.'</span>';
                    $descriptionHtml .= '  <span class="whitespace-nowrap ml-auto text-right">Rp'.number_format($abc->total, 0, ',', '.').'</span>';
                    $descriptionHtml .= '</div>';
                }
            }
            $descriptionHtml .= '</div>';

            $color = $uiConfig[$mrpType]['color'] ?? 'primary';
            $icon = $uiConfig[$mrpType]['icon'] ?? 'heroicon-m-chart-bar';

            $stats[] = Stat::make('Total GRS '.$mrpType, 'Rp'.number_format($totalGrsBulanIni, 0, ',', '.'))
                ->description(new HtmlString($descriptionHtml))
                ->color($color);
        }

        return $stats;
    }
}

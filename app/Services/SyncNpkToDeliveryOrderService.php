<?php

namespace App\Services;

use App\Models\DeliveryOrderReceipt;
use App\Models\DeliveryOrderReceiptDetail;
use App\Models\MonitoringNpk;
use App\Models\PurchaseOrderIssued;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SyncNpkToDeliveryOrderService
{
    public function sync(MonitoringNpk $npk): DeliveryOrderReceipt
    {
        return DB::transaction(function () use ($npk) {
            $actorId = Auth::id() ?? $npk->getAttribute('created_by');

            /** @var PurchaseOrderIssued $anchor */
            $anchor = $npk->purchaseOrderIssued()->firstOrFail();

            // Find existing DO or create new
            $dor = DeliveryOrderReceipt::where('monitoring_npk_id', $npk->id)->first() ?? new DeliveryOrderReceipt;

            $tgl103 = $npk->getAttribute('purchase_order_103_date');

            $payload = [
                'monitoring_npk_id' => $npk->id,
                'delivery_oder_no' => $npk->getAttribute('delivery_oder_number'),
                'location_id' => $npk->getAttribute('location_id'),
                'received_date' => $npk->getAttribute('received_date'),
                'stage' => $npk->getAttribute('stage'),
                'source_type' => 'Bahan Baku NPK',
                'status' => $dor->getAttribute('status') ?? 'Draft', // initial status
                'post_103' => $tgl103 ? Carbon::parse($tgl103)->startOfDay() : null,
                'received_by' => $dor->getAttribute('received_by') ?? $actorId,
                'created_by' => $dor->getAttribute('created_by') ?? $actorId,
            ];

            $dor->fill($payload)->save();

            // NPK Details
            $details = $npk->details()->get();

            $expectedItemNos = [];

            foreach ($details as $d) {
                // Find PO item using anchor PO number and detail item no
                $po = PurchaseOrderIssued::where('purchase_order_no', $anchor->purchase_order_no)
                    ->where('item_no', $d->getAttribute('item_no'))
                    ->first();

                if (! $po) {
                    continue;
                }

                $poItemNo = (int) $po->getAttribute('item_no');
                $expectedItemNos[] = $poItemNo;

                $detail = DeliveryOrderReceiptDetail::query()->firstOrNew([
                    'delivery_order_receipt_id' => $dor->getAttribute('id'),
                    'item_no' => $poItemNo,
                ]);

                $detail->fill([
                    'purchase_order_issued_id' => $po->id,
                    'quantity' => (string) $d->getAttribute('quantity'),
                    'material_code' => $po->getAttribute('material_code'),
                    'description' => $po->getAttribute('description'),
                    'uoi' => $po->getAttribute('uoi'),
                    'mrp_type' => $po->getAttribute('mrp_type'),
                    'material_type' => $po->getAttribute('material_type'),
                    'aac' => $po->getAttribute('aac'),
                    'abc_indicator' => $po->getAttribute('abc_indicator'),
                    'requisitioner' => $po->getAttribute('requisitioner'),
                    'location_id' => $npk->getAttribute('location_id'),
                    'is_qty_tolerance' => $d->getAttribute('is_qty_tolerance') ?? false,
                ])->save();
            }

            if (! empty($expectedItemNos)) {
                DeliveryOrderReceiptDetail::where('delivery_order_receipt_id', $dor->getAttribute('id'))
                    ->whereNotIn('item_no', $expectedItemNos)
                    ->delete();
            }

            return $dor;
        });
    }
}

<?php

namespace App\Services;

use App\Models\{
    MonitoringChemical,
    DeliveryOrderReceipt,
    DeliveryOrderReceiptDetail,
    PurchaseOrderIssued
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SyncChemicalToDeliveryOrderService
{
    public function sync(MonitoringChemical $chemical): DeliveryOrderReceipt
    {
        return DB::transaction(function () use ($chemical) {
            $actorId = Auth::id() ?? $chemical->getAttribute('created_by');

            /** @var PurchaseOrderIssued $po */
            $po = $chemical->purchaseOrderIssued()->firstOrFail();

            // Find existing DO or create new
            $dor = DeliveryOrderReceipt::where('monitoring_chemical_id', $chemical->id)->first() ?? new DeliveryOrderReceipt();

            $payload = [
                'monitoring_chemical_id' => $chemical->id,
                'delivery_oder_no' => $chemical->getAttribute('do_number'),
                'location_id' => $chemical->getAttribute('location_id'),
                'received_date' => $chemical->getAttribute('received_date'),
                'source_type' => 'Chemical/Karung',
                'status' => $dor->getAttribute('status') ?? 'Draft', // initial status
                'received_by' => $dor->getAttribute('received_by') ?? $actorId,
                'created_by' => $dor->getAttribute('created_by') ?? $actorId,
            ];

            $dor->fill($payload)->save();

            // Find existing DO Detail or create new
            $poItemNo = (int) $po->getAttribute('item_no');
            
            $detail = DeliveryOrderReceiptDetail::query()->firstOrNew([
                'delivery_order_receipt_id' => $dor->getAttribute('id'),
                'item_no' => $poItemNo,
            ]);

            $detail->fill([
                'purchase_order_issued_id' => $po->id,
                'quantity' => (string) $chemical->getAttribute('quantity'),
                'material_code' => $po->getAttribute('material_code'),
                'description' => $po->getAttribute('description'),
                'uoi' => $po->getAttribute('uoi'),
                'location_id' => $chemical->getAttribute('location_id'),
                'is_qty_tolerance' => $chemical->getAttribute('is_qty_tolerance') ?? false,
            ])->save();

            // Remove any other details that might have been there 
            // (since a MonitoringChemical is strictly 1:1 with PO)
            DeliveryOrderReceiptDetail::where('delivery_order_receipt_id', $dor->getAttribute('id'))
                ->where('item_no', '!=', $poItemNo)
                ->delete();

            return $dor;
        });
    }
}

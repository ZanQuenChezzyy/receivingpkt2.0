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

            // Ambil detail pertama untuk location_id utama DO
            $firstDetail = $chemical->monitoringChemicalDetails()->first();
            $mainLocationId = $firstDetail ? $firstDetail->location_id : null;

            // Find existing DO or create new
            $dor = DeliveryOrderReceipt::where('monitoring_chemical_id', $chemical->id)->first() ?? new DeliveryOrderReceipt();

            $payload = [
                'monitoring_chemical_id' => $chemical->id,
                'delivery_oder_no' => $chemical->getAttribute('do_number'),
                'location_id' => $mainLocationId,
                'received_date' => $chemical->getAttribute('received_date'),
                'source_type' => 'Chemical/Karung',
                'status' => $dor->getAttribute('status') ?? 'Draft', // initial status
                'received_by' => $dor->getAttribute('received_by') ?? $actorId,
                'created_by' => $dor->getAttribute('created_by') ?? $actorId,
            ];

            $dor->fill($payload)->save();

            $validItemNos = [];

            foreach ($chemical->monitoringChemicalDetails as $detailRow) {
                $po = $detailRow->purchaseOrderIssued;
                if (!$po) continue;

                $poItemNo = (int) $po->getAttribute('item_no');
                $validItemNos[] = $poItemNo;
                
                $detail = DeliveryOrderReceiptDetail::query()->firstOrNew([
                    'delivery_order_receipt_id' => $dor->getAttribute('id'),
                    'item_no' => $poItemNo,
                ]);

                $detail->fill([
                    'purchase_order_issued_id' => $po->id,
                    'quantity' => (string) $detailRow->quantity,
                    'material_code' => $po->getAttribute('material_code'),
                    'description' => $po->getAttribute('description'),
                    'uoi' => $po->getAttribute('uoi'),
                    'mrp_type' => $po->getAttribute('mrp_type'),
                    'material_type' => $po->getAttribute('material_type'),
                    'aac' => $po->getAttribute('aac'),
                    'abc_indicator' => $po->getAttribute('abc_indicator'),
                    'requisitioner' => $po->getAttribute('requisitioner'),
                    'location_id' => $detailRow->location_id,
                    'is_qty_tolerance' => $detailRow->is_qty_tolerance ?? false,
                ])->save();
            }

            // Remove any other details that might have been there
            if (!empty($validItemNos)) {
                DeliveryOrderReceiptDetail::where('delivery_order_receipt_id', $dor->getAttribute('id'))
                    ->whereNotIn('item_no', $validItemNos)
                    ->delete();
            } else {
                DeliveryOrderReceiptDetail::where('delivery_order_receipt_id', $dor->getAttribute('id'))
                    ->delete();
            }

            return $dor;
        });
    }
}

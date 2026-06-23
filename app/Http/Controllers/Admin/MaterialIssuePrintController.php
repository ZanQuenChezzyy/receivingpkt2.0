<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialIssue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MaterialIssuePrintController extends Controller
{
    public function print(MaterialIssue $materialIssue)
    {
        return $this->generatePdf(collect([$materialIssue]));
    }

    public function printBulk(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $records = MaterialIssue::whereIn('id', $ids)->get();

        if ($records->isEmpty()) {
            abort(404, 'No records found.');
        }

        return $this->generatePdf($records);
    }

    protected function generatePdf($records)
    {
        if (!$records instanceof \Illuminate\Database\Eloquent\Collection) {
            $records = \Illuminate\Database\Eloquent\Collection::make($records);
        }
        
        $records->load(['materialIssueDetails.deliveryOrderReceiptDetail', 'purchaseOrderIssued', 'createdBy']);
        
        $pdf = Pdf::loadView('pdf.mir', [
            'records' => $records,
        ]);
        
        // F4 size: 215 x 330 mm (609.45 x 935.43 pt)
        $pdf->setPaper([0, 0, 609.4488, 935.433], 'portrait');
        
        $filename = $records->count() === 1 
            ? 'MIR-' . str_replace('/', '-', $records->first()->mir_number) . '.pdf'
            : 'MIR-Bulk.pdf';

        return $pdf->stream($filename);
    }
}

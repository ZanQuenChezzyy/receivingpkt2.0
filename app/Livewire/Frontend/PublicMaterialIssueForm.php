<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\MaterialIssue;
use App\Models\MaterialIssueDetail;
use App\Models\DeliveryOrderReceiptDetail;
use App\Models\PurchaseOrderIssued;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PublicMaterialIssueForm extends Component
{
    // Form Properties
    public $diminta_oleh = '';
    public $npk = '';
    public $diterima_oleh = '';
    public $no_hp = '';
    public $departemen = '';
    public $bagian = '';
    
    // Signatures
    public $diminta_signature = null;
    public $disetujui_oleh = '';
    public $disetujui_signature = null;
    public $requiresIstekSignature = false;
    
    public $tanggal = '';
    public $purchase_order_issued_id = '';
    public $no_reservasi = '';
    public $no_jor_wo = '';
    public $no_alat = '';
    public $kode_biaya = '';
    public $digunakan_untuk = '';
    public $agreement = false;

    public $details = [];

    // Search properties
    #[Url(as: 'po')]
    public $po_search = '';
    public $available_pos = [];
    public $available_po_items = [];

    public $showSuccessMessage = false;
    public $showConfirmModal = false;

    public function mount()
    {
        $this->tanggal = now()->format('Y-m-d');
        $this->addDetail();
        $this->searchPOs();

        if (!empty($this->po_search) && count($this->available_pos) > 0) {
            $matchedPo = collect($this->available_pos)->firstWhere('purchase_order_no', $this->po_search);
            if ($matchedPo) {
                $this->purchase_order_issued_id = $matchedPo->id;
                $this->updatedPurchaseOrderIssuedId($this->purchase_order_issued_id);
            }
        }
    }

    public function updatedDimintaOleh($value)
    {
        $this->diterima_oleh = $value;
    }

    public function updatedPoSearch()
    {
        $this->searchPOs();
    }

    public function searchPOs()
    {
        $query = PurchaseOrderIssued::whereHas('deliveryOrderReceiptDetails');
        
        if (!empty($this->po_search)) {
            $query->where('purchase_order_no', 'like', '%' . $this->po_search . '%');
        }

        $this->available_pos = $query->limit(20)->get()->unique('purchase_order_no');
    }

    public function updatedPurchaseOrderIssuedId($id)
    {
        if ($id) {
            $poItem = PurchaseOrderIssued::find($id);
            if ($poItem) {
                $allPoItemIds = PurchaseOrderIssued::where('purchase_order_no', $poItem->purchase_order_no)->pluck('id');
                $rawItems = DeliveryOrderReceiptDetail::with(['locationReceiving', 'deliveryOrderReceipt'])
                    ->whereIn('purchase_order_issued_id', $allPoItemIds)
                    ->get();
                    
                $this->available_po_items = $rawItems->groupBy('purchase_order_issued_id')->map(function ($group) {
                    $first = $group->first();
                    
                    $combined_quantity = $group->sum('quantity');
                    $combined_issued = $group->sum(function($item) { return $item->issued_quantity; });
                    $combined_boh = $combined_quantity - $combined_issued;
                    
                    $locations = $group->map(fn($i) => $i->locationReceiving?->name)->filter()->unique()->implode(', ');
                    
                    $has_non_grs = $group->contains(function ($item) {
                        return $item->deliveryOrderReceipt && $item->deliveryOrderReceipt->document_code !== '105';
                    });
                    
                    return [
                        'id' => $first->purchase_order_issued_id, // Store as PO issued ID
                        'item_no' => $first->item_no,
                        'material_code' => $first->material_code,
                        'description' => $first->description,
                        'uoi' => $first->uoi,
                        'combined_boh' => $combined_boh,
                        'combined_locations' => $locations ?: 'Belum Diatur',
                        'has_non_grs' => $has_non_grs,
                    ];
                })->values()->toArray();
            } else {
                $this->available_po_items = [];
            }
        } else {
            $this->available_po_items = [];
        }

        // Reset details
        $this->details = [];
        $this->addDetail();
    }

    public function updatedDetails($value, $key)
    {
        // $key looks like "0.delivery_order_receipt_detail_id" or "0.diminta"
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if ($field === 'delivery_order_receipt_detail_id') {
                $detailId = $value;
                if ($detailId) {
                    $item = collect($this->available_po_items)->firstWhere('id', (int)$detailId) 
                         ?? collect($this->available_po_items)->firstWhere('id', (string)$detailId);
                         
                    if ($item) {
                        $this->details[$index]['stock_no'] = $item['material_code'];
                        $this->details[$index]['description'] = $item['description'];
                        $this->details[$index]['location'] = $item['combined_locations'];
                        $this->details[$index]['uoi'] = $item['uoi'];
                        $this->details[$index]['boh'] = $item['combined_boh'];
                    }
                } else {
                    $this->details[$index]['stock_no'] = '';
                    $this->details[$index]['description'] = '';
                    $this->details[$index]['location'] = '';
                    $this->details[$index]['uoi'] = '';
                    $this->details[$index]['boh'] = 0;
                }
            } elseif ($field === 'diminta') {
                // Auto fill diserahkan
                $this->details[$index]['diserahkan'] = $value;
            }
        }
        
        $this->checkIfIstekSignatureRequired();
    }

    public function addDetail()
    {
        $this->details[] = [
            'delivery_order_receipt_detail_id' => '',
            'stock_no' => '',
            'description' => '',
            'location' => '',
            'uoi' => '',
            'diminta' => '',
            'diserahkan' => '',
            'boh' => 0,
        ];
    }
    
    public function removeDetail($index)
    {
        if (count($this->details) > 1) {
            unset($this->details[$index]);
            $this->details = array_values($this->details);
            $this->checkIfIstekSignatureRequired();
        }
    }
    
    protected function checkIfIstekSignatureRequired()
    {
        $requires = false;
        foreach ($this->details as $detail) {
            $detailId = $detail['delivery_order_receipt_detail_id'] ?? null;
            if ($detailId) {
                $item = collect($this->available_po_items)->firstWhere('id', (int)$detailId) 
                     ?? collect($this->available_po_items)->firstWhere('id', (string)$detailId);
                     
                if ($item && !empty($item['has_non_grs'])) {
                    $requires = true;
                    break;
                }
            }
        }
        $this->requiresIstekSignature = $requires;
    }

    public function rules()
    {
        $rules = [
            'diminta_oleh' => 'required|string',
            'npk' => 'required|string',
            'diterima_oleh' => 'required|string',
            'no_hp' => 'required|string',
            'departemen' => 'required|string',
            'bagian' => 'required|string',
            'tanggal' => 'required|date',
            'purchase_order_issued_id' => 'required',
            'digunakan_untuk' => 'required|string',
            'agreement' => 'accepted',
            'diminta_signature' => 'required|string',
            'details.*.delivery_order_receipt_detail_id' => 'required',
            'details.*.diminta' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    // Extract index
                    $parts = explode('.', $attribute);
                    $index = $parts[1];
                    $boh = (float) ($this->details[$index]['boh'] ?? 0);
                    if ((float) $value > $boh) {
                        $fail("Kuantitas diminta melebihi sisa stok (BOH: {$boh}).");
                    }
                },
            ],
            'details.*.diserahkan' => 'required|numeric',
        ];
        
        if ($this->requiresIstekSignature) {
            $rules['disetujui_oleh'] = 'required|string';
            $rules['disetujui_signature'] = 'required|string';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'purchase_order_issued_id.required' => 'PO wajib dipilih.',
            'details.*.delivery_order_receipt_detail_id.required' => 'Material wajib dipilih.',
            'details.*.diminta.required' => 'Qty wajib diisi.',
            'details.*.diminta.numeric' => 'Qty harus berupa angka.',
            'details.*.diminta.min' => 'Qty harus lebih dari 0.',
            'agreement.accepted' => 'Anda harus menyetujui pernyataan ini sebelum mengirim form.',
            'diminta_signature.required' => 'Tanda tangan peminta wajib diisi.',
            'disetujui_oleh.required' => 'Nama ISTEK wajib diisi karena ada barang yang belum GRS.',
            'disetujui_signature.required' => 'Tanda tangan ISTEK wajib diisi karena ada barang yang belum GRS.',
        ];
    }

    public function confirmSubmit()
    {
        $this->validate();
        $this->showConfirmModal = true;
    }

    public function submit()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $mirNumber = 'MIR-' . date('Ymd') . '-' . Str::upper(Str::random(4));

            $issue = MaterialIssue::create([
                'mir_number' => $mirNumber,
                'tanggal' => $this->tanggal,
                'purchase_order_issued_id' => $this->purchase_order_issued_id,
                'no_hp' => $this->no_hp,
                'no_reservasi' => $this->no_reservasi,
                'departemen' => $this->departemen,
                'bagian' => $this->bagian,
                'no_jor_wo' => $this->no_jor_wo,
                'digunakan_untuk' => $this->digunakan_untuk,
                'no_alat' => $this->no_alat,
                'kode_biaya' => $this->kode_biaya,
                'diminta_oleh' => $this->diminta_oleh,
                'npk' => $this->npk,
                'diminta_signature' => $this->diminta_signature,
                'diterima_oleh' => $this->diterima_oleh,
                'disetujui_oleh' => $this->requiresIstekSignature ? $this->disetujui_oleh : null,
                'disetujui_signature' => $this->requiresIstekSignature ? $this->disetujui_signature : null,
                'created_by' => null,
            ]);

            foreach ($this->details as $detailData) {
                $poIssuedId = $detailData['delivery_order_receipt_detail_id']; // ini berisi purchase_order_issued_id sekarang
                $diminta = (float) $detailData['diminta'];
                
                // Ambil semua DO receipts untuk PO item ini, prioritaskan yang BOH-nya lebih dari 0
                $receipts = DeliveryOrderReceiptDetail::where('purchase_order_issued_id', $poIssuedId)
                                ->orderBy('id', 'asc')
                                ->get();
                                
                foreach ($receipts as $receipt) {
                    if ($diminta <= 0) break;
                    
                    $receiptBoh = (float) $receipt->quantity - (float) $receipt->issued_quantity;
                    if ($receiptBoh <= 0) continue;
                    
                    $take = min($receiptBoh, $diminta);
                    
                    MaterialIssueDetail::create([
                        'material_issue_id' => $issue->id,
                        'delivery_order_receipt_detail_id' => $receipt->id,
                        'diminta' => $take,
                        'diserahkan' => $take, // Default diserahkan = diminta
                        'boh' => $receiptBoh,
                    ]);
                    
                    $diminta -= $take;
                }
            }

            DB::commit();

            // Reset specific fields
            $this->diminta_oleh = '';
            $this->npk = '';
            $this->diterima_oleh = '';
            $this->no_hp = '';
            $this->departemen = '';
            $this->bagian = '';
            $this->purchase_order_issued_id = '';
            $this->po_search = '';
            $this->no_reservasi = '';
            $this->no_jor_wo = '';
            $this->no_alat = '';
            $this->kode_biaya = '';
            $this->digunakan_untuk = '';
            $this->diminta_signature = null;
            $this->disetujui_oleh = '';
            $this->disetujui_signature = null;
            $this->agreement = false;
            $this->details = [];
            $this->addDetail();
            
            $this->showConfirmModal = false;
            $this->showSuccessMessage = true;
            $this->dispatch('mir-submitted');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('submit', 'Gagal menyimpan pengajuan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.frontend.public-material-issue-form')->layout('components.layouts.frontend');
    }
}

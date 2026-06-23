<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak MIR</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .page {
            width: 100%;
            height: 100%;
            page-break-after: always;
        }
        .page:last-child {
            page-break-after: auto;
        }
        .mir-container {
            height: 48%; /* Takes up roughly half the page */
            box-sizing: border-box;
            position: relative;
        }
        .mir-separator {
            height: 2%;
            border-bottom: 1px dashed #999;
            margin-bottom: 2%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: -1px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 3px 5px;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .align-top { vertical-align: top; }
        
        .header-table td { padding: 3px 5px; }
        .items-table th, .items-table td { padding: 4px 5px; }
        .footer-table td { padding: 3px 5px; }
    </style>
</head>
<body>

    @foreach($records->chunk(2) as $chunk)
    <div class="page">
        @foreach($chunk as $index => $record)
        <div class="mir-container">
            <!-- Header Block -->
            <table class="header-table">
                <tr>
                    <td rowspan="6" class="text-center" style="width: 25%;">
                        <img src="{{ public_path('images/logo/logodasar.png') }}" alt="Logo Pupuk Kaltim" style="width: 130px; margin-bottom: 4px;">
                        <br>
                        <span class="font-bold" style="font-size: 12px;">PT PUPUK KALTIM<br>BONTANG</span>
                    </td>
                    <td colspan="2" class="text-center font-bold" style="font-size: 13px; width: 45%;">
                        PERMINTAAN PENGELUARAN BARANG
                    </td>
                    <td colspan="2" style="width: 30%;">
                        Tanggal &nbsp;&nbsp;&nbsp;: {{ $record->tanggal ? $record->tanggal->format('d M Y') : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 15%;">Departemen</td>
                    <td style="width: 30%;">: {{ $record->departemen }}</td>
                    <td style="width: 12%;">No MIR</td>
                    <td style="width: 18%;">: {{ $record->mir_number }}</td>
                </tr>
                <tr>
                    <td>Bagian</td>
                    <td>: {{ $record->bagian }}</td>
                    <td>No.HP</td>
                    <td>: {{ $record->no_hp }}</td>
                </tr>
                <tr>
                    <td>No. JOR/WO</td>
                    <td>: {{ $record->no_jor_wo }}</td>
                    <td>No.Reservasi</td>
                    <td>: {{ $record->no_reservasi }}</td>
                </tr>
                <tr>
                    <td>Dipakai</td>
                    <td>: {{ $record->digunakan_untuk }}</td>
                    <td colspan="2" rowspan="2" class="align-top font-bold" style="font-size: 12px;">
                        Nomor PO: &nbsp; {{ $record->purchaseOrderIssued?->purchase_order_no }}
                    </td>
                </tr>
                <tr>
                    <td>No. Alat</td>
                    <td>: {{ $record->no_alat }}</td>
                </tr>
            </table>

            <!-- Items Block -->
            <table class="items-table">
                <thead>
                    <tr class="text-center font-bold">
                        <td style="width: 28%;">Description</td>
                        <td style="width: 12%;">Stock No.</td>
                        <td style="width: 12%;">Lokasi</td>
                        <td style="width: 8%;">Diminta</td>
                        <td style="width: 10%;">Diserahkan</td>
                        <td style="width: 8%;">BOH</td>
                        <td style="width: 7%;">UOI</td>
                        <td style="width: 15%;">Kode Biaya</td>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $groupedDetails = $record->materialIssueDetails->groupBy(function($detail) {
                            return $detail->deliveryOrderReceiptDetail?->purchase_order_issued_id;
                        })->map(function($group) {
                            $first = $group->first();
                            $locations = $group->map(fn($d) => $d->deliveryOrderReceiptDetail?->locationReceiving?->name)->filter()->unique()->implode(', ');
                            
                            return (object)[
                                'description' => $first->deliveryOrderReceiptDetail?->description,
                                'material_code' => $first->deliveryOrderReceiptDetail?->material_code,
                                'location' => $locations ?: 'Belum Diatur',
                                'diminta' => $group->sum('diminta'),
                                'diserahkan' => $group->sum('diserahkan'),
                                'boh' => $group->sum('boh'),
                                'uoi' => $first->deliveryOrderReceiptDetail?->uoi,
                            ];
                        });
                    @endphp

                    @foreach($groupedDetails as $detail)
                    <tr>
                        <td>{{ $detail->description }}</td>
                        <td class="text-center">{{ $detail->material_code }}</td>
                        <td class="text-center">{{ $detail->location }}</td>
                        <td class="text-center">{{ (float) $detail->diminta }}</td>
                        <td class="text-center">{{ (float) $detail->diserahkan }}</td>
                        <td class="text-center">{{ (float) $detail->boh }}</td>
                        <td class="text-center">{{ $detail->uoi }}</td>
                        <td class="text-center">{{ $record->kode_biaya }}</td>
                    </tr>
                    @endforeach
                    
                    {{-- Fill empty rows to make the table look complete like the physical form --}}
                    @php $emptyRows = 6 - $groupedDetails->count(); @endphp
                    @for($i = 0; $i < ($emptyRows > 0 ? $emptyRows : 0); $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Signatures Block -->
            <table class="footer-table">
                <tr class="font-bold">
                    <td style="width: 20%;">Diminta</td>
                    <td style="width: 20%;">Disetujui</td>
                    <td style="width: 20%;">Diketahui</td>
                    <td style="width: 20%;">Diserahkan</td>
                    <td style="width: 20%;">Diterima</td>
                </tr>
                <tr>
                    <td>Dept: {{ $record->departemen }}</td>
                    <td>Dept: {{ $record->disetujui_oleh || $record->disetujui_signature ? 'ISTEK' : '' }}</td>
                    <td>Dept:</td>
                    <td>Dept:</td>
                    <td>Dept: {{ $record->departemen }}</td>
                </tr>
                <tr>
                    <td style="height: 60px; vertical-align: middle; text-align: center;">
                        @if($record->diminta_signature)
                            <img src="{{ $record->diminta_signature }}" style="max-height: 55px; max-width: 100%; display: inline-block;">
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td style="height: 60px; vertical-align: middle; text-align: center;">
                        @if($record->disetujui_signature)
                            <img src="{{ $record->disetujui_signature }}" style="max-height: 55px; max-width: 100%; display: inline-block;">
                        @else
                            &nbsp;
                        @endif
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="height: 60px; vertical-align: middle; text-align: center;">
                        @if($record->diminta_signature)
                            <img src="{{ $record->diminta_signature }}" style="max-height: 55px; max-width: 100%; display: inline-block;">
                        @else
                            &nbsp;
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Nama: {{ $record->diminta_oleh }}</td>
                    <td>Nama: {{ $record->disetujui_oleh }}</td>
                    <td>Nama: {{ $record->diketahui_oleh }}</td>
                    <td>Nama: {{ $record->diserahkan_oleh }}</td>
                    <td>Nama: {{ $record->diterima_oleh }}</td>
                </tr>
                <tr>
                    <td>NPK: {{ $record->npk }}</td>
                    <td>NPK: {{ $record->disetujui_npk }}</td>
                    <td>NPK:</td>
                    <td>NPK:</td>
                    <td>NPK: {{ $record->npk }}</td>
                </tr>
            </table>
        </div>
        
        @if($index === 0 && count($chunk) === 2)
            <div class="mir-separator"></div>
        @endif
        
        @endforeach
    </div>
    @endforeach

</body>
</html>

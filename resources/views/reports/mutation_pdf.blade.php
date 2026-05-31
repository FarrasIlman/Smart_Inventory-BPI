<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Mutasi Stok Bahan Baku</title>
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }
        @page {
            size: A4;
            margin: 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        
        /* Header Layout menggunakan tabel murni */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
        }
        .header-logo-cell {
            width: 50%;
            vertical-align: top;
        }
        .header-title-cell {
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        .company-name {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px 0;
        }
        .system-name {
            font-size: 9pt;
            font-weight: bold;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .report-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            margin: 0 0 5px 0;
        }
        .report-date {
            font-size: 9pt;
            color: #64748b;
            margin: 0;
        }

        /* Table Design */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e293b;
            margin-bottom: 12px;
            border-left: 3px solid #2563eb;
            padding-left: 8px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px 12px;
            border-bottom: 1px solid #cbd5e1;
        }
        .data-table td {
            padding: 10px 12px;
            font-size: 8.5pt;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .ref-id {
            font-size: 7.5pt;
            color: #94a3b8;
            font-weight: bold;
            display: block;
        }
        .mat-name {
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        
        /* Badges & Text Tipe */
        .tipe-text {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .t-masuk { color: #16a34a; }
        .t-penyesuaian { color: #d97706; }
        .t-keluar { color: #dc2626; }

        .amt-masuk { color: #16a34a; font-weight: bold; }
        .amt-penyesuaian { color: #d97706; font-weight: bold; }
        .amt-keluar { color: #dc2626; font-weight: bold; }

        .keterangan-text {
            font-size: 8.5pt;
            color: #64748b;
            font-style: italic;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-logo-cell">
                <div class="company-name">BUMIPUTERA PERSADA INDUSTRI</div>
                <div class="system-name">Inventory System</div>
            </td>
            <td class="header-title-cell">
                <div class="report-title">Laporan Mutasi Stok</div>
                <div class="report-date">Periode: {{ date('d/m/Y', strtotime($start_date)) }} - {{ date('d/m/Y', strtotime($end_date)) }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Riwayat Pergerakan Logistik</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Waktu Transaksi</th>
                <th style="width: 30%;">Nama Bahan Baku</th>
                <th style="width: 15%; text-align: center;">Tipe</th>
                <th style="width: 15%; text-align: center;">Jumlah Mutasi</th>
                <th style="width: 25%;">Keterangan / Referensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mutations as $mut)
                @php
                    $tipe = strtolower($mut->tipe_transaksi);
                    
                    // Setup style class berdasarkan tipe mutasi
                    $style = match($tipe) {
                        'masuk'       => ['type_class' => 't-masuk', 'amt_class' => 'amt-masuk', 'symbol' => '+'],
                        'penyesuaian' => ['type_class' => 't-penyesuaian', 'amt_class' => 'amt-penyesuaian', 'symbol' => '±'],
                        default       => ['type_class' => 't-keluar', 'amt_class' => 'amt-keluar', 'symbol' => '-']
                    };
                @endphp
                <tr>
                    <td>
                        <span style="font-weight: bold; color: #475569;">{{ date('d/m/Y H:i', strtotime($mut->created_at)) }}</span>
                        <span class="ref-id">REF #{{ $mut->id_movement }}</span>
                    </td>
                    <td>
                        <span class="mat-name">{{ $mut->nama_bahanbaku }}</span>
                    </td>
                    <td class="text-center">
                        <span class="tipe-text {{ $style['type_class'] }}">{{ $mut->tipe_transaksi }}</span>
                    </td>
                    <td class="text-center">
                        <span class="{{ $style['amt_class'] }}">
                            {{ $style['symbol'] }} {{ number_format($mut->jumlah, 2) }}
                        </span>
                        <span style="font-size: 7.5pt; color: #94a3b8; font-weight: bold;">{{ strtoupper($mut->satuan) }}</span>
                    </td>
                    <td>
                        <div class="keterangan-text">{{ $mut->keterangan ?? '-' }}</div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
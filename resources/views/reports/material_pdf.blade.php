<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris Bahan Baku</title>
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
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
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
            color: #4f46e5;
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

        /* Summary Boxes */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-left: -10px;
            margin-right: -10px;
            margin-bottom: 30px;
        }
        .summary-card {
            width: 33.333%;
            padding: 15px 20px;
            border-radius: 12px;
            vertical-align: top;
        }
        .card-normal {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .card-danger {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
        }
        .card-dark {
            background-color: #0f172a;
            color: #ffffff;
        }
        .card-label {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
        }
        .card-danger .card-label { color: #ef4444; }
        .card-dark .card-label { color: #94a3b8; }
        
        .card-value {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .card-danger .card-value { color: #dc2626; }
        .card-dark .card-value { color: #38bdf8; }

        /* Table Design */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e293b;
            margin-bottom: 12px;
            border-left: 3px solid #4f46e5;
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
            font-size: 9pt;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .mat-name {
            font-weight: bold;
            color: #1e293b;
        }
        .mat-id {
            font-size: 8pt;
            color: #94a3b8;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-danger { color: #ef4444; font-weight: bold; }
        
        /* Status Text Styling */
        .status-danger { color: #dc2626; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        .status-safe { color: #16a34a; font-weight: bold; text-transform: uppercase; font-size: 8pt; }
        .satuan-tag {
            background-color: #f1f5f9;
            color: #475569;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
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
                <div class="report-title">Laporan Stok Bahan Baku</div>
                <div class="report-date">Tanggal Cetak: {{ date('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td class="summary-card card-normal">
                <div class="card-label">Total Jenis Bahan</div>
                <div class="card-value">{{ $summary['total_jenis'] }} <span style="font-size: 9pt; color: #94a3b8; font-weight: normal;">SKU</span></div>
            </td>
            <td class="summary-card card-danger">
                <div class="card-label">Stok Dibawah Minimum</div>
                <div class="card-value">{{ $summary['stok_kritis'] }} <span style="font-size: 9pt; color: #f87171; font-weight: normal;">Bahan</span></div>
            </td>
            <td class="summary-card card-dark">
                <div class="card-label">Estimasi Nilai Aset</div>
                <div class="card-value" style="font-size: 13pt; padding-top: 4px;">Rp {{ number_format($summary['total_aset'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">List Inventaris</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 38%;">Nama Bahan Baku</th>
                <th style="width: 15%; text-align: center;">Stok Saat Ini</th>
                <th style="width: 12%; text-align: center;">Satuan</th>
                <th style="width: 20%;">Status Stok</th>
                <th style="width: 15%; text-align: right;">Nilai Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materials as $m)
                @php $isLow = $m->stok <= $m->stok_minimum; @endphp
                <tr>
                    <td>
                        <div class="mat-name">{{ strtoupper($m->nama_bahanbaku) }}</div>
                        <div class="mat-id">ID: #MAT-{{ $m->id_bahanbaku }}</div>
                    </td>
                    <td class="text-center {{ $isLow ? 'text-danger' : '' }}" style="font-weight: bold;">
                        {{ number_format($m->stok, 2) }}
                    </td>
                    <td class="text-center">
                        <span class="satuan-tag">{{ strtoupper($m->satuan) }}</span>
                    </td>
                    <td>
                        @if($isLow)
                            <span class="status-danger">Perlu Tambahan</span>
                        @else
                            <span class="status-safe">Stock Aman</span>
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp {{ number_format($m->stok * ($m->harga ?? 0), 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
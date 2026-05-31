<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Penjualan</title>
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
        
        /* Header Layout (Tabel Anti-Jebol) */
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

        /* Summary Row Metrics */
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
        .card-primary {
            background-color: #2563eb;
            color: #ffffff;
            border: 1px solid #1d4ed8;
        }
        .card-normal {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .card-label {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
        }
        .card-primary .card-label { color: #93c5fd; }
        
        .card-value {
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .card-primary .card-value { color: #ffffff; }

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
            font-size: 9pt;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Clean Text Status (Anti Tanda Tanya) */
        .status-text {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .st-selesai { color: #16a34a; }
        .st-dikirim { color: #2563eb; }
        .st-produksi { color: #d97706; }
        .st-siap { color: #7c3aed; }
        .st-menunggu { color: #ea580c; }
        .st-perlu { color: #e11d48; }
        .st-default { color: #475569; }
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
                <div class="report-title">Laporan Analisis Penjualan</div>
                <div class="report-date">Periode: {{ date('d/m/Y', strtotime($start_date)) }} - {{ date('d/m/Y', strtotime($end_date)) }}</div>
            </td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td class="summary-card card-primary">
                <div class="card-label">Total Omzet</div>
                <div class="card-value">Rp {{ number_format($summary['total_omzet'], 0, ',', '.') }}</div>
            </td>
            <td class="summary-card card-normal">
                <div class="card-label">Total Pesanan</div>
                <div class="card-value">{{ $summary['total_pesanan'] }} <span style="font-size: 9pt; color: #94a3b8; font-weight: normal;">Orders</span></div>
            </td>
            <td class="summary-card card-normal">
                <div class="card-label">Total Produk Terjual</div>
                <div class="card-value">{{ $summary['total_qty'] }} <span style="font-size: 9pt; color: #94a3b8; font-weight: normal;">Pcs</span></div>
            </td>
        </tr>
    </table>

    <div class="section-title">Daftar Transaksi Penjualan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">Pelanggan</th>
                <th style="width: 25%;">Produk</th>
                <th style="width: 8%; text-align: center;">Qty</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 12%; text-align: right;">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                @php
                    $st_db = strtolower($order->status_order);
                    $status_style = match($st_db) {
                        'selesai' => ['class' => 'st-selesai', 'label' => 'Selesai'],
                        'dikirim' => ['class' => 'st-dikirim', 'label' => 'Dikirim'],
                        'sedang produksi', 'produksi' => ['class' => 'st-produksi', 'label' => 'Produksi'],
                        'siap produksi' => ['class' => 'st-siap', 'label' => 'Siap Produksi'],
                        'menunggu bahan', 'menunggu' => ['class' => 'st-menunggu', 'label' => 'Menunggu Bahan'],
                        'perlu dikirim' => ['class' => 'st-perlu', 'label' => 'Perlu Dikirim'],
                        default => ['class' => 'st-default', 'label' => strtoupper($order->status_order)],
                    };
                @endphp
                <tr>
                    <td>{{ date('d/m/Y', strtotime($order->created_at)) }}</td>
                    <td style="text-transform: uppercase;">{{ $order->nama_pelanggan }}</td>
                    <td style="text-transform: uppercase; font-weight: bold;">{{ $order->nama_produk }}</td>
                    <td class="text-center">{{ $order->jumlah_pesanan }}</td>
                    <td>
                        <span class="status-text {{ $status_style['class'] }}">{{ $status_style['label'] }}</span>
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #2563eb;">
                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
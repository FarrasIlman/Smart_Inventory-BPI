<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analisis Produksi</title>
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
        
        /* Header Layout */
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
            color: #d97706;
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

        /* Summary Grid Layout */
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-left: -8px;
            margin-right: -8px;
            margin-bottom: 25px;
        }
        .summary-card {
            width: 25%;
            padding: 12px 15px;
            border-radius: 10px;
            vertical-align: top;
        }
        .card-total { background-color: #f8fafc; border: 1px solid #e2e8f0; }
        .card-antrean { background-color: #f5f3ff; border: 1px solid #ddd6fe; }
        .card-proses { background-color: #fffbeb; border: 1px solid #fde68a; }
        .card-selesai { background-color: #f0fdf4; border: 1px solid #bbf7d0; }
        
        .card-label {
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 4px;
        }
        .card-antrean .card-label { color: #6d28d9; }
        .card-proses .card-label { color: #b45309; }
        .card-selesai .card-label { color: #15803d; }
        
        .card-value {
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .card-antrean .card-value { color: #7c3aed; }
        .card-proses .card-value { color: #d97706; }
        .card-selesai .card-value { color: #16a34a; }

        /* Table Design */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e293b;
            margin-bottom: 12px;
            border-left: 3px solid #d97706;
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
        .order-id { font-size: 7.5pt; color: #94a3b8; font-weight: bold; display: block; }
        .pelanggan-name { font-weight: bold; color: #334155; text-transform: uppercase; }
        .product-name { color: #1e293b; text-transform: uppercase; font-weight: bold; }
        .product-subtext { font-size: 7.5pt; color: #64748b; font-style: italic; }
        .text-center { text-align: center; }
        
        /* Clean Text Badges */
        .status-badge { font-size: 8pt; font-weight: bold; text-transform: uppercase; }
        .t-potong { color: #475569; }
        .t-branding { color: #4f46e5; }
        .t-jahit { color: #b45309; }
        .t-finishing { color: #0891b2; }
        .t-qc { color: #e11d48; }
        .t-selesai { color: #16a34a; }
        .t-menunggu { color: #7c3aed; }

        .s-menunggu { color: #ea580c; }
        .s-siap { color: #7c3aed; }
        .s-produksi { color: #b45309; }
        .s-perlu { color: #e11d48; }
        .s-dikirim { color: #2563eb; }
        .s-selesai { color: #16a34a; }
        .s-default { color: #64748b; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-logo-cell">
                <div class="company-name">BUMIPUTERA PERSADA INDUSTRI</div>
                <div class="system-name">Inventory Management System</div>
            </td>
            <td class="header-title-cell">
                <div class="report-title">Laporan Analisis Produksi</div>
                <div class="report-date">Periode: {{ date('d/m/Y', strtotime($start_date)) }} - {{ date('d/m/Y', strtotime($end_date)) }}</div>
            </td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td class="summary-card card-total">
                <div class="card-label">Total Produksi</div>
                <div class="card-value">{{ $summary['total_produksi'] }}</div>
            </td>
            <td class="summary-card card-antrean">
                <div class="card-label">Antrean (Siap)</div>
                <div class="card-value">{{ $summary['siap_proses'] }}</div>
            </td>
            <td class="summary-card card-proses">
                <div class="card-label">Sedang Proses</div>
                <div class="card-value">{{ $summary['sedang_jalan'] }}</div>
            </td>
            <td class="summary-card card-selesai">
                <div class="card-label">Selesai Produksi</div>
                <div class="card-value">{{ $summary['tahap_akhir'] }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Daftar Produksi Pesanan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tgl Mulai</th>
                <th style="width: 25%;">Order / Pelanggan</th>
                <th style="width: 30%;">Produk</th>
                <th style="width: 15%; text-align: center;">Tahap Kerja</th>
                <th style="width: 15%;">Status Order</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productions as $prod)
                @php
                    $status_db = strtolower($prod->status_order);
                    $tahap_db  = strtolower($prod->tahap_produksi);

                    // Logic override label tanpa menggunakan karakter emoji
                    if($status_db == 'siap produksi') {
                        $tahap_style = ['class' => 't-menunggu', 'lbl' => 'Menunggu Produksi'];
                    } else {
                        $tahap_style = match($tahap_db) {
                            'potong'        => ['class' => 't-potong', 'lbl' => 'Potong'],
                            'branding'      => ['class' => 't-branding', 'lbl' => 'Branding'],
                            'jahit'         => ['class' => 't-jahit', 'lbl' => 'Jahit'],
                            'finishing'     => ['class' => 't-finishing', 'lbl' => 'Finishing'],
                            'quality check' => ['class' => 't-qc', 'lbl' => 'QC Check'],
                            'selesai'       => ['class' => 't-selesai', 'lbl' => 'Selesai'],
                            default         => ['class' => 't-potong', 'lbl' => strtoupper($prod->tahap_produksi)],
                        };
                    }

                    $status_style = match($status_db) {
                        'menunggu bahan' => ['class' => 's-menunggu', 'lbl' => 'Menunggu Bahan'],
                        'siap produksi'  => ['class' => 's-siap', 'lbl' => 'Siap Produksi'],
                        'produksi'       => ['class' => 's-produksi', 'lbl' => 'Produksi'],
                        'perlu dikirim'  => ['class' => 's-perlu', 'lbl' => 'Perlu Dikirim'],
                        'dikirim'        => ['class' => 's-dikirim', 'lbl' => 'Dikirim'],
                        'selesai'        => ['class' => 's-selesai', 'lbl' => 'Selesai'],
                        default          => ['class' => 's-default', 'lbl' => strtoupper($prod->status_order)],
                    };
                @endphp
                <tr>
                    <td>{{ date('d M Y', strtotime($prod->created_at)) }}</td>
                    <td>
                        <span class="order-id">#ORD-{{ $prod->id_order }}</span>
                        <span class="pelanggan-name">{{ $prod->nama_pelanggan }}</span>
                    </td>
                    <td>
                        <div class="product-name">{{ $prod->nama_produk }}</div>
                        <div class="product-subtext">{{ $prod->jumlah_pesanan }} Pcs Terpesan</div>
                    </td>
                    <td class="text-center">
                        <span class="status-badge {{ $tahap_style['class'] }}">{{ $tahap_style['lbl'] }}</span>
                    </td>
                    <td>
                        <span class="status-badge {{ $status_style['class'] }}">{{ $status_style['lbl'] }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
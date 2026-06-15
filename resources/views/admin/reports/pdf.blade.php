<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - Mamitha Bakery</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #b45309;
            font-size: 22px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 11px;
        }
        .meta-info {
            margin-bottom: 15px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
        }
        .meta-table .label {
            color: #666;
            width: 100px;
        }
        .meta-table .val {
            font-weight: bold;
        }
        .stats-table {
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 10px;
            border-collapse: separate;
        }
        .stats-cell {
            width: 25%;
            padding: 12px 10px;
            background-color: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 6px;
            text-align: center;
        }
        .stats-cell.blue {
            background-color: #dbeafe;
            border-color: #bfdbfe;
        }
        .stats-cell.green {
            background-color: #d1fae5;
            border-color: #a7f3d0;
        }
        .stats-cell.purple {
            background-color: #f3e8ff;
            border-color: #e9d5ff;
        }
        .stats-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .stats-value {
            font-size: 14px;
            font-weight: bold;
            color: #111;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #b45309;
            margin-top: 20px;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 6px 8px;
            font-weight: bold;
            border-bottom: 2px solid #e5e7eb;
            font-size: 10px;
            color: #4b5563;
        }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
            vertical-align: middle;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-pickup {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-delivery {
            background-color: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Mamitha Bakery</h1>
        <p>Laporan Keuangan & Analisis Penjualan</p>
    </div>

    <div class="meta-info">
        <table class="meta-table">
            <tr>
                <td class="label">Periode</td>
                <td class="val">: {{ ucfirst($period) }}</td>
                <td class="label" style="text-align:right;">Tanggal Cetak</td>
                <td class="val" style="text-align:right;">: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Rentang Data</td>
                <td class="val" colspan="3">: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</td>
            </tr>
        </table>
    </div>

    <table class="stats-table">
        <tr>
            <td class="stats-cell">
                <div class="stats-label">Pendapatan</div>
                <div class="stats-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </td>
            <td class="stats-cell blue">
                <div class="stats-label">Total Pesanan</div>
                <div class="stats-value">{{ $totalOrders }}</div>
            </td>
            <td class="stats-cell green">
                <div class="stats-label">Rata-rata Order</div>
                <div class="stats-value">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</div>
            </td>
            <td class="stats-cell purple">
                <div class="stats-label">Volume Terjual</div>
                <div class="stats-value">{{ $totalItemsSold }} pcs</div>
            </td>
        </tr>
    </table>

    <div class="section-title">10 Produk Terlaris</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%" class="text-center">No</th>
                <th style="width: 55%">Nama Produk</th>
                <th style="width: 20%" class="text-center">Jumlah Terjual</th>
                <th style="width: 20%" class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product ? $item->product->name : 'Produk Terhapus' }}</td>
                    <td class="text-center">{{ $item->total_qty }} pcs</td>
                    <td class="text-right">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data penjualan produk</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Rincian Pendapatan Harian</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th class="text-center">Jumlah Order</th>
                <th class="text-right">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailyRevenue as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                    <td class="text-center">{{ $day->orders }}</td>
                    <td class="text-right">Rp {{ number_format($day->revenue, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada data harian</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-before: always;"></div>

    <div class="header">
        <h1>Mamitha Bakery</h1>
        <p>Aktivitas Transaksi Selesai (Periode Terpilih)</p>
    </div>

    <div class="section-title">Daftar Transaksi Selesai</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%">No. Order</th>
                <th style="width: 25%">Pelanggan</th>
                <th style="width: 20%">Waktu Order</th>
                <th style="width: 15%" class="text-center">Metode</th>
                <th style="width: 25%" class="text-right">Total Belanja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allCompletedOrders as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>
                        {{ $order->customer->name }}<br>
                        <span style="font-size: 8px; color: #666;">{{ $order->customer->phone }}</span>
                    </td>
                    <td>{{ $order->order_date->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $order->type === 'pickup' ? 'badge-pickup' : 'badge-delivery' }}">
                            {{ $order->type === 'pickup' ? 'Pickup' : 'Delivery' }}
                        </span>
                    </td>
                    <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada transaksi selesai pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

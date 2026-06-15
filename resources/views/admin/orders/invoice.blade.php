<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: auto; padding: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 0; }
        hr { border: none; border-top: 1px dashed #000; margin: 8px 0; }
        .header { margin-bottom: 10px; }
        .items td:last-child { text-align: right; }
        .items td:first-child { padding-right: 8px; }
    </style>
</head>
<body>
    <div class="header text-center">
        <h2 style="margin:0;">{{ $storeName }}</h2>
        <p style="margin:2px 0;">{{ $storeAddress }}</p>
        <p style="margin:2px 0;">{{ $storePhone }}</p>
    </div>
    <hr>
    <p><strong>INVOICE</strong></p>
    <p>No: {{ $order->order_number }}</p>
    <p>Tgl Order: {{ $order->order_date->format('d/m/Y H:i') }}</p>
    <p>Ambil: {{ $order->pickup_date->format('d/m/Y') }}</p>
    <p>Pelanggan: {{ $order->customer->name }}</p>
    <p>WA: {{ $order->customer->phone }}</p>
    @if($order->address)<p>Alamat: {{ $order->address }}</p>@endif
    <hr>
    <table class="items">
        <thead>
            <tr style="border-bottom: 1px dashed #000;">
                <th style="text-align:left; padding-bottom:4px;">Deskripsi</th>
                <th style="text-align:right; padding-bottom:4px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td colspan="2" class="bold" style="padding-top:6px;">{{ $item->product->name }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="color:#555; padding-left:8px;">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                <td style="text-align:right;">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <p class="text-right bold">Total: Rp{{ number_format($order->total, 0, ',', '.') }}</p>
    <p class="text-right">Status: {{ $order->statusLabel() }}</p>
    <hr>
    <p class="text-center">Terima kasih telah berbelanja!</p>
    <p class="text-center" style="font-size:10px;">Barang yang sudah dibeli tidak dapat ditukar</p>
    <script>
        window.print();
        setTimeout(() => window.close(), 1000);
    </script>
</body>
</html>

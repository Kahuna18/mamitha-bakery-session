<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dapur - {{ $order->order_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: auto; padding: 10px; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        hr { border: none; border-top: 1px dashed #000; margin: 8px 0; }
    </style>
</head>
<body>
    <div class="text-center">
        <h2 style="margin:0;">{{ $storeName }}</h2>
        <p style="margin:2px 0;"><strong>ORDER DAPUR</strong></p>
    </div>
    <hr>
    <p>No: {{ $order->order_number }}</p>
    <p>Pelanggan: {{ $order->customer->name }}</p>
    <p>WA: {{ $order->customer->phone }}</p>
    <p>Tgl Order: {{ $order->order_date->format('d/m/Y H:i') }}</p>
    <p>Tgl Ambil: {{ $order->pickup_date->format('d/m/Y') }}</p>
    <p>Tipe: {{ $order->type == 'pickup' ? 'Ambil di Toko' : 'Diantar' }}</p>
    @if($order->address)<p>Alamat: {{ $order->address }}</p>@endif
    <hr>
    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px dashed #000;">
                <th style="text-align:left; padding-bottom:4px;">Deskripsi</th>
                <th style="text-align:right; padding-bottom:4px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td colspan="2" class="bold" style="padding-top:6px;">
                    {{ $item->product->name }}
                    @if($item->variant)
                    <span style="font-size:10px; font-weight:normal; color:#666;">({{ $item->variant->name }})</span>
                    @endif
                </td>
            </tr>
            @if($item->note)
            <tr>
                <td colspan="2" style="font-size:10px; font-style:italic; padding-left:8px; color:#555;">Catatan: "{{ $item->note }}"</td>
            </tr>
            @endif
            <tr style="border-bottom: 1px solid #eee;">
                <td style="color:#555; padding-left:8px;">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                <td style="text-align:right;">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <p style="text-align:right;" class="bold">Total: Rp{{ number_format($order->total, 0, ',', '.') }}</p>
    @if($order->notes)
    <hr>
    <p><strong>Catatan:</strong> {{ $order->notes }}</p>
    @endif
    <hr>
    <p class="text-center bold">--- SELESAI ---</p>
    <script>
        window.print();
        setTimeout(() => window.close(), 1000);
    </script>
</body>
</html>

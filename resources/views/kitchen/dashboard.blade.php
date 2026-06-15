@extends('layouts.kitchen')

@section('title', 'Dashboard Kitchen')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Kitchen</h1>
        <p class="text-gray-500 text-sm">{{ $storeName }}</p>
    </div>
    <div class="flex gap-3 mt-2 md:mt-0">
        <div class="bg-yellow-100 rounded-lg px-4 py-2 text-center">
            <p class="text-xs text-yellow-700 font-medium">Menunggu</p>
            <p class="text-xl font-bold text-yellow-700">{{ $pendingCount }}</p>
        </div>
        <div class="bg-orange-100 rounded-lg px-4 py-2 text-center">
            <p class="text-xs text-orange-700 font-medium">Diproses</p>
            <p class="text-xl font-bold text-orange-700">{{ $producingCount }}</p>
        </div>
        <div class="bg-green-100 rounded-lg px-4 py-2 text-center">
            <p class="text-xs text-green-700 font-medium">Selesai Hari Ini</p>
            <p class="text-xl font-bold text-green-700">{{ $completedToday }}</p>
        </div>
    </div>
</div>

@if($tasks->count() > 0)
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($tasks as $task)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 {{ $task->status == 'producing' ? 'ring-2 ring-orange-300' : '' }}">
        <div class="flex justify-between items-start mb-3">
            <div>
                <p class="font-bold text-gray-800 text-lg">{{ $task->order->customer->name }}</p>
                <p class="text-xs text-gray-500">{{ $task->order->order_number }}</p>
            </div>
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $task->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700' }}">
                {{ $task->status == 'pending' ? 'Menunggu' : 'Sedang Dibuat' }}
            </span>
        </div>

        <div class="border-t border-gray-100 pt-3 space-y-1">
            @foreach($task->order->items as $item)
            <div class="flex justify-between text-sm">
                <span>{{ $item->product->name }}</span>
                <span class="font-medium">x{{ $item->quantity }}</span>
            </div>
            @endforeach
        </div>

        @if($task->order->notes)
        <div class="mt-2 bg-yellow-50 rounded-lg p-2 text-xs text-yellow-700">
            <span class="font-medium">Catatan:</span> {{ $task->order->notes }}
        </div>
        @endif

        <div class="mt-3 text-xs text-gray-500">
            Deadline: {{ $task->order->pickup_date->format('d/m/Y') }}
            @if($task->order->type == 'delivery')
            <span class="ml-2 px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded">Diantar</span>
            @else
            <span class="ml-2 px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded">Ambil</span>
            @endif
        </div>

        <div class="mt-3 flex gap-2">
            @if($task->status == 'pending')
            <form action="{{ route('kitchen.task.update-status', $task) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="status" value="producing">
                <button type="submit" class="w-full px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition">
                    Mulai Buat
                </button>
            </form>
            @endif
            @if($task->status == 'producing')
            <form action="{{ route('kitchen.task.update-status', $task) }}" method="POST" class="flex-1">
                @csrf
                <input type="hidden" name="status" value="done">
                <button type="submit" class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    Selesai
                </button>
            </form>
            @endif
            <a href="{{ route('kitchen.print', $task->order) }}" target="_blank" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                Print
            </a>
            <button onclick="printBluetooth(this)" class="px-3 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium rounded-lg transition"
                data-order='{!! json_encode([
                    "storeName" => $storeName,
                    "type" => "kitchen",
                    "orderNumber" => $task->order->order_number,
                    "pickupDate" => $task->order->pickup_date->format("d/m/Y"),
                    "customerName" => $task->order->customer->name,
                    "customerPhone" => $task->order->customer->phone,
                    "orderType" => $task->order->type,
                    "address" => $task->order->address,
                    "notes" => $task->order->notes,
                    "total" => $task->order->total,
                    "items" => $task->order->items->map(fn($i) => [
                        "name" => $i->product->name,
                        "quantity" => $i->quantity,
                        "price" => $i->price,
                        "subtotal" => $i->subtotal,
                    ])->toArray(),
                ]) !!}'>
                🖨️ BT Print
            </button>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-12">
    <p class="text-gray-400 text-lg">Tidak ada tugas di dapur saat ini.</p>
    <p class="text-gray-400 text-sm mt-1">Semua pesanan sudah selesai.</p>
</div>
@endif
@endsection

@push('scripts')
<script>
    async function printBluetooth(btn) {
        try {
            const data = JSON.parse(btn.getAttribute('data-order'));
            await ThermalPrinter.printReceipt(data);
        } catch (err) {
            if (err.name !== 'NotFoundError') {
                console.error('Bluetooth print error:', err);
            }
        }
    }
</script>
@endpush


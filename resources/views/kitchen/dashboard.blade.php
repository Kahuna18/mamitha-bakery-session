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
            <div class="mb-1">
                <div class="flex justify-between text-sm">
                    <span>
                        {{ $item->product->name }}
                        @if($item->variant)
                        <span class="text-xs text-purple-600 font-semibold">({{ $item->variant->name }})</span>
                        @endif
                    </span>
                    <span class="font-medium">x{{ $item->quantity }}</span>
                </div>
                @if($item->note)
                <p class="text-[11px] text-amber-700 font-medium italic mt-0.5">" {{ $item->note }} "</p>
                @endif
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
                    "items" => $task->order->items->map(function($i) {
                        $name = $i->product->name;
                        if ($i->variant) {
                            $name .= ' (' . $i->variant->name . ')';
                        }
                        if ($i->note) {
                            $name .= "\n  * " . $i->note;
                        }
                        return [
                            "name" => $name,
                            "quantity" => $i->quantity,
                            "price" => $i->price,
                            "subtotal" => $i->subtotal,
                        ];
                    })->toArray(),
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

<!-- Alarm Modal Overlay -->
<div id="alarm-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden transition-all duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-orange-100 dark:border-gray-700 text-center transform scale-95 transition-all duration-300" id="alarm-card">
        <div class="w-20 h-20 bg-orange-100 dark:bg-orange-950/30 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner mb-5 animate-bounce">
            🔔
        </div>
        <h3 class="text-2xl font-black text-gray-800 dark:text-white">Pesanan Baru Masuk!</h3>
        <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Ada pesanan baru yang perlu segera diproses di dapur.</p>
        
        <div class="mt-6">
            <button onclick="acceptAndReload()" class="w-full py-3.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-2xl shadow-lg transition active:scale-95 flex items-center justify-center gap-2">
                <span>👨‍🍳</span> Terima & Muat Ulang
            </button>
        </div>
    </div>
</div>
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

    let lastTaskId = {{ \App\Models\KitchenTask::max('id') ?: 0 }};
    let alarmInterval = null;
    let audioCtx = null;

    function initAudio() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
    }

    // Synthesize alarm sound using Web Audio API
    function playAlarmSound() {
        initAudio();
        if (!audioCtx) return;
        
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }

        const playTone = (frequency, startTime, duration, type = 'sine') => {
            const osc = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            
            osc.type = type;
            osc.frequency.setValueAtTime(frequency, startTime);
            
            gainNode.gain.setValueAtTime(0.35, startTime);
            gainNode.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);
            
            osc.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            osc.start(startTime);
            osc.stop(startTime + duration);
        };

        // Ringtone double tone chime
        playTone(880, audioCtx.currentTime, 0.15, 'triangle');
        playTone(1200, audioCtx.currentTime + 0.15, 0.35, 'sine');
    }

    function startAlarm() {
        const modal = document.getElementById('alarm-modal');
        const card = document.getElementById('alarm-card');
        if (modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }, 10);
        }

        playAlarmSound();
        if (!alarmInterval) {
            alarmInterval = setInterval(playAlarmSound, 1500);
        }
    }

    function stopAlarm() {
        if (alarmInterval) {
            clearInterval(alarmInterval);
            alarmInterval = null;
        }
    }

    function acceptAndReload() {
        stopAlarm();
        window.location.reload();
    }

    // Polling function
    async function checkNewOrders() {
        try {
            const response = await fetch(`{{ route('kitchen.check-new-tasks') }}?last_task_id=${lastTaskId}`);
            if (!response.ok) throw new Error('Response error');
            
            const data = await response.json();
            if (data.new_tasks_count > 0) {
                startAlarm();
            }
        } catch (error) {
            console.error('Error polling for new orders:', error);
        }
    }

    // Poll every 10 seconds
    setInterval(checkNewOrders, 10000);

    // Init audio context on user interaction
    document.addEventListener('click', () => {
        initAudio();
    }, { once: true });
</script>
@endpush


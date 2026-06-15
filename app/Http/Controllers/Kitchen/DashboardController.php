<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\KitchenTask;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tasks = KitchenTask::with(['order.customer', 'order.items.product'])
            ->whereIn('status', ['pending', 'producing'])
            ->latest()
            ->get();

        $completedToday = KitchenTask::where('status', 'done')
            ->whereDate('completed_at', today())
            ->count();

        $pendingCount = $tasks->where('status', 'pending')->count();
        $producingCount = $tasks->where('status', 'producing')->count();

        $storeName = Setting::getValue('store_name');

        return view('kitchen.dashboard', compact(
            'tasks', 'completedToday', 'pendingCount', 'producingCount', 'storeName'
        ));
    }

    public function updateStatus(Request $request, KitchenTask $task)
    {
        $request->validate([
            'status' => 'required|in:producing,done',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'producing') {
            $data['started_at'] = now();
            $data['user_id'] = auth()->id();
            $task->order->update(['status' => 'producing']);
        }

        if ($request->status === 'done') {
            $data['completed_at'] = now();
            $task->order->update(['status' => 'ready']);
        }

        $task->update($data);

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }

    public function print($id)
    {
        $order = Order::with(['customer', 'items.product'])->findOrFail($id);
        $storeName = Setting::getValue('store_name');

        return view('kitchen.print', compact('order', 'storeName'));
    }

    public function checkNewTasks(Request $request)
    {
        $lastTaskId = $request->get('last_task_id', 0);
        
        $newTasksCount = KitchenTask::where('id', '>', $lastTaskId)
            ->where('status', 'pending')
            ->count();
            
        return response()->json([
            'new_tasks_count' => $newTasksCount,
            'latest_task_id' => KitchenTask::max('id') ?: 0
        ]);
    }
}

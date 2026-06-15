<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getReportData($request);
        return view('admin.reports.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request, true);
        $pdf = Pdf::loadView('admin.reports.pdf', $data);
        
        $filename = 'Laporan-Keuangan-' . $data['period'] . '-' . $data['startDate']->format('Ymd') . '-' . $data['endDate']->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportCsv(Request $request)
    {
        $data = $this->getReportData($request, true);
        
        $filename = 'Laporan-Keuangan-' . $data['period'] . '-' . $data['startDate']->format('Ymd') . '-' . $data['endDate']->format('Ymd') . '.csv';
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Title
            fputcsv($file, ['Mamitha Bakery - Laporan Keuangan']);
            fputcsv($file, ['Periode', ucfirst($data['period'])]);
            fputcsv($file, ['Rentang Data', $data['startDate']->format('d/m/Y') . ' - ' . $data['endDate']->format('d/m/Y')]);
            fputcsv($file, ['Tanggal Cetak', Carbon::now()->format('d/m/Y H:i')]);
            fputcsv($file, []);
            
            // Summary Cards
            fputcsv($file, ['RINGKASAN UTAMA']);
            fputcsv($file, ['Total Pendapatan', 'Total Pesanan', 'Rata-rata Belanja', 'Volume Item Terjual', 'Pelanggan Baru']);
            fputcsv($file, [
                $data['totalRevenue'],
                $data['totalOrders'],
                $data['averageOrderValue'],
                $data['totalItemsSold'],
                $data['newCustomers']
            ]);
            fputcsv($file, []);
            
            // Top Selling Products
            fputcsv($file, ['10 PRODUK TERLARIS']);
            fputcsv($file, ['Rank', 'Nama Produk', 'Jumlah Terjual (pcs)', 'Total Pendapatan']);
            foreach ($data['topProducts'] as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->product ? $item->product->name : 'Produk Terhapus',
                    $item->total_qty,
                    $item->total_revenue
                ]);
            }
            fputcsv($file, []);
            
            // Daily Revenue
            fputcsv($file, ['RINCIAN PENDAPATAN HARIAN']);
            fputcsv($file, ['Tanggal', 'Jumlah Order', 'Pendapatan']);
            foreach ($data['dailyRevenue'] as $day) {
                fputcsv($file, [
                    Carbon::parse($day->date)->format('d/m/Y'),
                    $day->orders,
                    $day->revenue
                ]);
            }
            fputcsv($file, []);
            
            // Transactions List
            fputcsv($file, ['DAFTAR TRANSAKSI SELESAI']);
            fputcsv($file, ['Nomor Order', 'Nama Pelanggan', 'WhatsApp', 'Waktu Order', 'Tipe', 'Total Belanja']);
            foreach ($data['allCompletedOrders'] as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer->name,
                    $order->customer->phone,
                    $order->order_date->format('d/m/Y H:i'),
                    ucfirst($order->type),
                    $order->total
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    private function getReportData(Request $request, $isExport = false)
    {
        $period = $request->get('period', 'month'); // today, week, month, year, custom
        $startDate = null;
        $endDate = Carbon::now()->endOfDay();

        switch ($period) {
            case 'today':
                $startDate = Carbon::today();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->get('start_date', Carbon::now()->startOfMonth()));
                $endDate = Carbon::parse($request->get('end_date', Carbon::now()))->endOfDay();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
        }

        // ── Summary Cards ────────────────────────────────────────────
        $totalRevenue = Order::whereBetween('order_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $totalOrders = Order::whereBetween('order_date', [$startDate, $endDate])->count();

        $completedOrders = Order::whereBetween('order_date', [$startDate, $endDate])
            ->where('status', 'done')
            ->count();

        $cancelledOrders = Order::whereBetween('order_date', [$startDate, $endDate])
            ->where('status', 'cancelled')
            ->count();

        $totalItemsSold = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled');
        })->sum('quantity');

        $averageOrderValue = $totalOrders > 0
            ? Order::whereBetween('order_date', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->avg('total')
            : 0;

        $newCustomers = Customer::whereBetween('created_at', [$startDate, $endDate])->count();

        // ── Previous Period Comparison ────────────────────────────────
        $periodLength = $startDate->diffInDays($endDate);
        $prevStart = (clone $startDate)->subDays($periodLength + 1);
        $prevEnd = (clone $startDate)->subDay()->endOfDay();

        $prevRevenue = Order::whereBetween('order_date', [$prevStart, $prevEnd])
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $prevOrders = Order::whereBetween('order_date', [$prevStart, $prevEnd])->count();

        $revenueGrowth = $prevRevenue > 0
            ? round((($totalRevenue - $prevRevenue) / $prevRevenue) * 100, 1)
            : ($totalRevenue > 0 ? 100 : 0);

        $ordersGrowth = $prevOrders > 0
            ? round((($totalOrders - $prevOrders) / $prevOrders) * 100, 1)
            : ($totalOrders > 0 ? 100 : 0);

        // ── Daily Revenue Chart (last 30 days or custom range) ──────
        $dailyRevenue = Order::select(
            DB::raw('DATE(order_date) as date'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(*) as orders')
        )
            ->whereBetween('order_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Order Status Breakdown (Pie Chart) ──────────────────────
        $statusBreakdown = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('order_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        // ── Top 10 Best-Selling Products ─────────────────────────────
        $topProducts = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue')
        )
            ->whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('order_date', [$startDate, $endDate])
                    ->where('status', '!=', 'cancelled');
            })
            ->with('product:id,name,price,image')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(10)
            ->get();

        // ── Revenue by Order Type (pickup vs delivery) ──────────────
        $revenueByType = Order::select(
            'type',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as revenue')
        )
            ->whereBetween('order_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => [
                    'count' => $item->count,
                    'revenue' => $item->revenue,
                ]];
            });

        // ── Monthly Trend (12 months) ───────────────────────────────
        $monthlyTrend = Order::select(
            DB::raw("strftime('%Y-%m', order_date) as month"),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(*) as orders')
        )
            ->where('status', '!=', 'cancelled')
            ->where('order_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Check if DB is MySQL – adjust date function accordingly
        if (config('database.default') === 'mysql') {
            $monthlyTrend = Order::select(
                DB::raw("DATE_FORMAT(order_date, '%Y-%m') as month"),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
                ->where('status', '!=', 'cancelled')
                ->where('order_date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        // ── Recent Completed Orders ─────────────────────────────────
        $recentCompletedOrders = Order::with('customer')
            ->where('status', 'done')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->latest('order_date')
            ->take(10)
            ->get();

        // ── All Completed Orders (for PDF/CSV) ───────────────────────
        $allCompletedOrders = collect();
        if ($isExport) {
            $allCompletedOrders = Order::with('customer')
                ->where('status', 'done')
                ->whereBetween('order_date', [$startDate, $endDate])
                ->latest('order_date')
                ->get();
        }

        // ── Hourly Distribution (peak hours) ────────────────────────
        $hourlyOrders = Order::select(
            DB::raw("CAST(strftime('%H', order_date) AS INTEGER) as hour"),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('order_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        if (config('database.default') === 'mysql') {
            $hourlyOrders = Order::select(
                DB::raw("HOUR(order_date) as hour"),
                DB::raw('COUNT(*) as count')
            )
                ->whereBetween('order_date', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
        }

        return compact(
            'period', 'startDate', 'endDate',
            'totalRevenue', 'totalOrders', 'completedOrders', 'cancelledOrders',
            'totalItemsSold', 'averageOrderValue', 'newCustomers',
            'revenueGrowth', 'ordersGrowth',
            'dailyRevenue', 'statusBreakdown',
            'topProducts', 'revenueByType', 'monthlyTrend',
            'recentCompletedOrders', 'allCompletedOrders', 'hourlyOrders'
        );
    }
}

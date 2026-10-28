<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function canViewReports()
    {
        return Auth::check() && in_array(Auth::user()->role, ['owner', 'cashier']);
    }

    private function parseDateRange(Request $request): array
    {
        try {
            $from = $request->filled('from')
                ? Carbon::parse($request->from)->startOfDay()
                : now()->subDays(29)->startOfDay();

            $to = $request->filled('to')
                ? Carbon::parse($request->to)->endOfDay()
                : now()->endOfDay();
        } catch (\Exception $e) {
            $from = now()->subDays(29)->startOfDay();
            $to = now()->endOfDay();
        }

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        if ($from->diffInDays($to) > 365) {
            $from = $to->copy()->subDays(365);
        }

        return [$from, $to];
    }

    private function buildReportData(Carbon $from, Carbon $to): array
    {
        $totalRevenue = (float) Order::where('payment_status', 'paid')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('total_amount');

        $totalOrders = Order::whereBetween('created_at', [$from, $to])->count();
        $totalGallons = (int) Order::whereBetween('created_at', [$from, $to])->sum('quantity');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $outstandingAmount = (float) Order::where('payment_status', 'unpaid')
            ->whereIn('status', ['delivered', 'completed'])
            ->sum('total_amount');

        $paidOrdersCount = Order::where('payment_status', 'paid')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to])
            ->count();

        $unpaidOrdersCount = Order::where('payment_status', 'unpaid')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $dailyStats = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(quantity) as gallons')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $dailyRevenue = Order::where('payment_status', 'paid')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to])
            ->selectRaw('DATE(paid_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $dateLabels = [];
        $revenueData = [];
        $ordersData = [];
        $gallonsData = [];

        $current = $from->copy()->startOfDay();
        $end = $to->copy()->endOfDay();
        while ($current->lte($end)) {
            $dateStr = $current->format('Y-m-d');
            $dateLabels[] = $current->format('M d');
            $revenueData[] = (float) ($dailyRevenue[$dateStr]->revenue ?? 0);
            $ordersData[] = (int) ($dailyStats[$dateStr]->orders ?? 0);
            $gallonsData[] = (int) ($dailyStats[$dateStr]->gallons ?? 0);
            $current->addDay();
        }

        $statusCounts = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statusData = [
            'pending' => $statusCounts['pending'] ?? 0,
            'assigned' => $statusCounts['assigned'] ?? 0,
            'on_delivery' => $statusCounts['on_delivery'] ?? 0,
            'delivered' => $statusCounts['delivered'] ?? 0,
            'completed' => $statusCounts['completed'] ?? 0,
            'cancelled' => $statusCounts['cancelled'] ?? 0,
        ];

        $paymentCounts = Order::where('payment_status', 'paid')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $paymentData = [
            'cash' => [
                'count' => (int) ($paymentCounts['cash']->count ?? 0),
                'total' => (float) ($paymentCounts['cash']->total ?? 0),
            ],
            'gcash' => [
                'count' => (int) ($paymentCounts['gcash']->count ?? 0),
                'total' => (float) ($paymentCounts['gcash']->total ?? 0),
            ],
            'bank_transfer' => [
                'count' => (int) ($paymentCounts['bank_transfer']->count ?? 0),
                'total' => (float) ($paymentCounts['bank_transfer']->total ?? 0),
            ],
        ];

        $topCustomers = User::where('role', 'customer')
            ->whereHas('orders', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->withCount(['orders as order_count' => function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            }])
            ->withSum(['orders as total_spent' => function ($q) use ($from, $to) {
                $q->where('payment_status', 'paid')
                  ->whereNotNull('paid_at')
                  ->whereBetween('paid_at', [$from, $to]);
            }], 'total_amount')
            ->withSum(['orders as total_gallons' => function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            }], 'quantity')
            ->orderByDesc('order_count')
            ->limit(10)
            ->get();

        $riderPerformance = User::where('role', 'rider')
            ->withCount(['assignedOrders as delivered_count' => function ($q) use ($from, $to) {
                $q->where('status', 'delivered')
                  ->whereNotNull('delivered_at')
                  ->whereBetween('delivered_at', [$from, $to]);
            }])
            ->withSum(['assignedOrders as total_collected' => function ($q) use ($from, $to) {
                $q->where('payment_status', 'paid')
                  ->whereNotNull('paid_at')
                  ->whereBetween('paid_at', [$from, $to]);
            }], 'total_amount')
            ->having('delivered_count', '>', 0)
            ->orderByDesc('delivered_count')
            ->get();

        // ============================================
        // A8 - PROJECTIONS (now correctly defined INSIDE buildReportData)
        // ============================================
        $daysInPeriod = (int) $from->copy()->startOfDay()->diffInDays($to->copy()->startOfDay()) + 1;        $dailyAvgRevenue = $daysInPeriod > 0 ? $totalRevenue / $daysInPeriod : 0;
        $dailyAvgOrders = $daysInPeriod > 0 ? $totalOrders / $daysInPeriod : 0;
        $dailyAvgGallons = $daysInPeriod > 0 ? $totalGallons / $daysInPeriod : 0;

        $projected30dRevenue = $dailyAvgRevenue * 30;
        $projected30dOrders = (int) round($dailyAvgOrders * 30);
        $projected30dGallons = (int) round($dailyAvgGallons * 30);

        return [
            'from' => $from,
            'to' => $to,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'totalGallons' => $totalGallons,
            'avgOrderValue' => $avgOrderValue,
            'outstandingAmount' => $outstandingAmount,
            'paidOrdersCount' => $paidOrdersCount,
            'unpaidOrdersCount' => $unpaidOrdersCount,
            'dateLabels' => $dateLabels,
            'revenueData' => $revenueData,
            'ordersData' => $ordersData,
            'gallonsData' => $gallonsData,
            'statusData' => $statusData,
            'paymentData' => $paymentData,
            'topCustomers' => $topCustomers,
            'riderPerformance' => $riderPerformance,

            // ✅ A8 - Projections
            'daysInPeriod' => $daysInPeriod,
            'dailyAvgRevenue' => $dailyAvgRevenue,
            'dailyAvgOrders' => $dailyAvgOrders,
            'dailyAvgGallons' => $dailyAvgGallons,
            'projected30dRevenue' => $projected30dRevenue,
            'projected30dOrders' => $projected30dOrders,
            'projected30dGallons' => $projected30dGallons,
        ];
    }

    public function index(Request $request)
    {
        if (!$this->canViewReports()) {
            abort(403, 'Unauthorized access.');
        }

        [$from, $to] = $this->parseDateRange($request);
        $data = $this->buildReportData($from, $to);

        return view('owner.reports', $data);
    }

    public function exportCsv(Request $request)
    {
        if (!$this->canViewReports()) {
            abort(403, 'Unauthorized access.');
        }

        [$from, $to] = $this->parseDateRange($request);

        $orders = Order::with(['customer', 'rider', 'paidBy'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at', 'asc')
            ->get();

        $filename = 'water-station-report_' . $from->format('Y-m-d') . '_to_' . $to->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders, $from, $to) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, ['Poblacion Water Refilling Station — Order Report']);
            fputcsv($file, ['From', $from->format('M d, Y')]);
            fputcsv($file, ['To', $to->format('M d, Y')]);
            fputcsv($file, ['Generated', now()->format('M d, Y h:i A')]);

            // ✅ FIX: Auth::user()->name → full_name
            fputcsv($file, ['Generated By', Auth::user()->full_name . ' (' . ucfirst(Auth::user()->role) . ')']);
            fputcsv($file, []);

            fputcsv($file, [
                'Order #', 'Date Placed', 'Customer', 'Contact', 'Type',
                'Quantity', 'Unit Price', 'Delivery Fee', 'Total Amount',
                'Status', 'Payment Status', 'Payment Method', 'Reference',
                'Rider', 'Delivered At', 'Paid At',
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->getCustomerName(),
                    $order->getCustomerContact(),
                    $order->is_walk_in ? 'Walk-in' : 'Online',
                    $order->quantity,
                    number_format($order->unit_price, 2, '.', ''),
                    number_format($order->delivery_fee, 2, '.', ''),
                    number_format($order->total_amount, 2, '.', ''),
                    $order->getStatusLabel(),
                    $order->getPaymentStatusLabel(),
                    $order->payment_method !== 'none' ? $order->payment_method : '',
                    $order->payment_reference ?? '',
                    $order->rider->full_name ?? '', // ✅ FIX
                    $order->delivered_at?->format('Y-m-d H:i') ?? '',
                    $order->paid_at?->format('Y-m-d H:i') ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
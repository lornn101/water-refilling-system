<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Business Reports
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                {{ Auth::user()->role === 'owner' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800' }}">
                {{ Auth::user()->role === 'owner' ? '👑 Owner' : '💼 Cashier' }}
            </span>
        </div>
    </x-slot>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-break { page-break-inside: avoid; }
        }
    </style>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Date Range Filter (hidden when printing) --}}
            <div class="no-print bg-white rounded-xl shadow-lg p-4 mb-6">
                <form method="GET" action="{{ route('owner.reports') }}" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="border-gray-300 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="border-gray-300 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        🔍 Apply
                    </button>
                    <a href="{{ route('owner.reports') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                        ↺ Reset
                    </a>

                    {{-- 🆕 QUICK FILTER BUTTONS --}}
                    <div class="flex gap-2 border-l border-gray-300 pl-3 ml-1">
                        <a href="{{ route('owner.reports', ['from' => now()->startOfMonth()->format('Y-m-d'), 'to' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition text-sm font-medium border border-indigo-200">
                           📅 This Month
                        </a>
                        <a href="{{ route('owner.reports', ['from' => now()->startOfYear()->format('Y-m-d'), 'to' => now()->format('Y-m-d')]) }}" 
                           class="px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition text-sm font-medium border border-indigo-200">
                           🗓️ This Year
                        </a>
                    </div>

                    <div class="ml-auto flex gap-2">
                        <a href="{{ route('owner.reports.export-csv', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
                           class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-medium">
                            📥 Export CSV
                        </a>
                        <button type="button" onclick="window.print()" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium">
                            🖨️ Print
                        </button>
                    </div>
                </form>
            </div>

            {{-- Report Header (visible in print) --}}
            <div class="hidden print:block text-center mb-4">
                <h1 class="text-2xl font-bold">Poblacion Water Refilling Station</h1>
                <p class="text-sm">Report Period: {{ $from->format('M d, Y') }} — {{ $to->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500">Generated: {{ now()->format('M d, Y h:i A') }}</p>
            </div>

                        {{-- Period Indicator --}}
            <div class="mb-4 flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    📅 Showing data from <strong>{{ $from->format('M d, Y') }}</strong> to <strong>{{ $to->format('M d, Y') }}</strong>
                    <span class="text-gray-400">({{ $daysInPeriod }} days)</span>
                </p>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl shadow-lg p-5 text-white print-break">
                    <p class="text-emerald-100 text-xs uppercase tracking-wider">💰 Revenue (Paid)</p>
                    <p class="text-2xl font-bold mt-1">₱{{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-xs text-emerald-100 mt-1">{{ $paidOrdersCount }} paid orders</p>
                </div>
                <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-xl shadow-lg p-5 text-white print-break">
                    <p class="text-orange-100 text-xs uppercase tracking-wider">⚠️ Outstanding</p>
                    <p class="text-2xl font-bold mt-1">₱{{ number_format($outstandingAmount, 2) }}</p>
                    <p class="text-xs text-orange-100 mt-1">Unpaid (all time)</p>
                </div>
                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-lg p-5 text-white print-break">
                    <p class="text-blue-100 text-xs uppercase tracking-wider">📦 Orders Placed</p>
                    <p class="text-2xl font-bold mt-1">{{ $totalOrders }}</p>
                    <p class="text-xs text-blue-100 mt-1">{{ $unpaidOrdersCount }} unpaid</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg p-5 text-white print-break">
                    <p class="text-purple-100 text-xs uppercase tracking-wider">💧 Gallons Sold</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($totalGallons) }}</p>
                    <p class="text-xs text-purple-100 mt-1">Total quantity</p>
                </div>
                <div class="bg-gradient-to-br from-gray-600 to-gray-800 rounded-xl shadow-lg p-5 text-white print-break">
                    <p class="text-gray-300 text-xs uppercase tracking-wider">📈 Avg Order Value</p>
                    <p class="text-2xl font-bold mt-1">₱{{ number_format($avgOrderValue, 2) }}</p>
                    <p class="text-xs text-gray-300 mt-1">Per order</p>
                </div>
            </div>

            {{-- Charts Row 1 --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-5 print-break">
                    <h3 class="font-semibold text-gray-800 mb-3">💰 Revenue Trend</h3>
                    <div style="height: 280px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-5 print-break">
                    <h3 class="font-semibold text-gray-800 mb-3">📊 Status Distribution</h3>
                    <div style="height: 280px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Charts Row 2 --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-5 print-break">
                    <h3 class="font-semibold text-gray-800 mb-3">📦 Orders per Day</h3>
                    <div style="height: 280px;">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-5 print-break">
                    <h3 class="font-semibold text-gray-800 mb-3">💳 Payment Methods</h3>
                    <div style="height: 280px;">
                        <canvas id="paymentChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Tables Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Top Customers --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden print-break">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">🏆 Top Customers</h3>
                        <span class="text-xs text-gray-500">By orders placed</span>
                    </div>
                    @if($topCustomers->isEmpty())
                        <div class="p-6 text-center text-gray-500 text-sm">No customer data in this period.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Orders</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Gallons</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Spent</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($topCustomers as $index => $customer)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                <p class="font-medium text-gray-800">{{ $customer->full_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $customer->email }}</p>
                                            </td>
                                            <td class="px-4 py-2 text-center text-sm font-medium text-gray-800">{{ $customer->order_count }}</td>
                                            <td class="px-4 py-2 text-center text-sm text-gray-500">{{ $customer->total_gallons ?? 0 }}</td>
                                            <td class="px-4 py-2 text-right text-sm font-semibold text-emerald-600">₱{{ number_format($customer->total_spent ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Rider Performance --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden print-break">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">🛵 Rider Performance</h3>
                        <span class="text-xs text-gray-500">By deliveries completed</span>
                    </div>
                    @if($riderPerformance->isEmpty())
                        <div class="p-6 text-center text-gray-500 text-sm">No rider data in this period.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rider</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Delivered</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Collected</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($riderPerformance as $index => $rider)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                <p class="font-medium text-gray-800">{{ $rider->full_name }}</p>
                                                <p class="text-xs text-gray-500">{{ $rider->email }}</p>
                                            </td>
                                            <td class="px-4 py-2 text-center text-sm font-medium text-gray-800">{{ $rider->delivered_count }}</td>
                                            <td class="px-4 py-2 text-right text-sm font-semibold text-emerald-600">₱{{ number_format($rider->total_collected ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

             {{-- ============================================ --}}
            {{-- A8 - PROJECTION / FORECAST --}}
            {{-- ============================================ --}}
            <div class="mt-8 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl shadow-xl border border-indigo-200 p-6 print-break">
                <div class="flex items-center mb-4">
                    <span class="text-3xl mr-3">🔮</span>
                    <div>
                        <h3 class="text-xl font-bold text-indigo-900">Forecast / Projection</h3>
                        <p class="text-sm text-indigo-600">Based on {{ $daysInPeriod }} days of data — projected to next 30 days</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl p-5 border-l-4 border-emerald-500 shadow-sm">
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">💵 Projected 30-Day Revenue</p>
                        <p class="text-3xl font-bold text-emerald-600 mt-2">₱{{ number_format($projected30dRevenue, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-2">
                            Daily avg: <strong>₱{{ number_format($dailyAvgRevenue, 2) }}</strong>
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-5 border-l-4 border-blue-500 shadow-sm">
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">📦 Projected 30-Day Orders</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2">{{ $projected30dOrders }}</p>
                        <p class="text-xs text-gray-500 mt-2">
                            Daily avg: <strong>{{ number_format($dailyAvgOrders, 2) }}</strong> orders
                        </p>
                    </div>

                    <div class="bg-white rounded-xl p-5 border-l-4 border-purple-500 shadow-sm">
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium">💧 Projected 30-Day Gallons</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ number_format($projected30dGallons) }}</p>
                        <p class="text-xs text-gray-500 mt-2">
                            Daily avg: <strong>{{ number_format($dailyAvgGallons, 2) }}</strong> gallons
                        </p>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-indigo-100/50 rounded-lg">
                    <p class="text-xs text-indigo-800">
                        <strong>ℹ️ Note:</strong> Projections are calculated as <em>(total ÷ days in period) × 30</em>. This assumes demand remains consistent with the selected period. Longer periods produce more reliable projections.
                    </p>
                </div>
            </div>

            {{-- Footer note --}}
            <div class="mt-6 text-center text-xs text-gray-400 print:block">
                <p>Poblacion Water Refilling Station • Report generated {{ now()->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        // ============================================
        // Chart Data from PHP
        // ============================================
        const dateLabels = @json($dateLabels);
        const revenueData = @json($revenueData);
        const ordersData = @json($ordersData);
        const statusData = @json($statusData);
        const paymentData = @json($paymentData);

        const currencyFormat = (val) => '₱' + Number(val).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // ============================================
        // 1. Revenue Trend (Line Chart)
        // ============================================
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: dateLabels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: revenueData,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) { return currencyFormat(context.parsed.y); }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: (value) => '₱' + value }
                    },
                    x: {
                        ticks: { maxRotation: 45, minRotation: 45, font: { size: 10 } }
                    }
                }
            }
        });

        // ============================================
        // 2. Orders per Day (Bar Chart)
        // ============================================
        new Chart(document.getElementById('ordersChart'), {
            type: 'bar',
            data: {
                labels: dateLabels,
                datasets: [{
                    label: 'Orders',
                    data: ordersData,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45, minRotation: 45, font: { size: 10 } } }
                }
            }
        });

        // ============================================
        // 3. Status Distribution (Doughnut)
        // ============================================
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Assigned', 'On Delivery', 'Delivered', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        statusData.pending,
                        statusData.assigned,
                        statusData.on_delivery,
                        statusData.delivered,
                        statusData.completed,
                        statusData.cancelled,
                    ],
                    backgroundColor: [
                        'rgba(234, 179, 8, 0.8)',   // yellow
                        'rgba(59, 130, 246, 0.8)',  // blue
                        'rgba(168, 85, 247, 0.8)',  // purple
                        'rgba(34, 197, 94, 0.8)',   // green
                        'rgba(16, 185, 129, 0.8)',  // emerald
                        'rgba(239, 68, 68, 0.8)',   // red
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } }
                },
                cutout: '60%',
            }
        });

        // ============================================
        // 4. Payment Methods (Doughnut)
        // ============================================
        const cashTotal = paymentData.cash.count;
        const gcashTotal = paymentData.gcash.count;
        const bankTotal = paymentData.bank_transfer.count;

        new Chart(document.getElementById('paymentChart'), {
            type: 'doughnut',
            data: {
                labels: ['💵 Cash', '📱 GCash', '🏦 Bank Transfer'],
                datasets: [{
                    data: [cashTotal, gcashTotal, bankTotal],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                    ],
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const counts = [cashTotal, gcashTotal, bankTotal];
                                const totals = [paymentData.cash.total, paymentData.gcash.total, paymentData.bank_transfer.total];
                                const idx = context.dataIndex;
                                return context.label + ': ' + counts[idx] + ' (₱' + Number(totals[idx]).toLocaleString('en-PH', { minimumFractionDigits: 2 }) + ')';
                            }
                        }
                    }
                },
                cutout: '60%',
            }
        });
    </script>
</x-app-layout>
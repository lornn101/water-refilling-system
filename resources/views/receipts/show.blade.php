<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .receipt-container { box-shadow: none !important; border: none !important; }
        }
        body { font-family: 'Courier New', monospace; }
    </style>
</head>
<body class="bg-gray-100 py-8">

    <div class="max-w-md mx-auto">
        {{-- Action Buttons --}}
        <div class="no-print mb-4 flex justify-between">
            <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-800 text-sm">← Back</a>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                🖨️ Print Receipt
            </button>
        </div>

        {{-- Receipt --}}
        <div class="receipt-container bg-white shadow-xl rounded-lg p-6 border border-gray-200">
            {{-- Header --}}
            <div class="text-center border-b border-dashed border-gray-300 pb-4 mb-4">
                <h1 class="text-xl font-bold text-gray-800">{{ \App\Models\Setting::get('business_name', 'Poblacion Water Refilling Station') }}</h1>
                <p class="text-xs text-gray-600 mt-1">{{ \App\Models\Setting::get('business_address', 'Poblacion, Bilar, Bohol') }}</p>
                <p class="text-xs text-gray-600">📞 {{ \App\Models\Setting::get('business_contact', '0912-345-6789') }}</p>
            </div>

            {{-- Order Info --}}
            <div class="text-xs mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Receipt #:</span>
                    <span class="font-bold">R-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-gray-600">Date:</span>
                    <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-gray-600">Type:</span>
                    <span>{{ $order->is_walk_in ? '🏪 Walk-in' : '🌐 Online' }}</span>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-gray-600">Customer:</span>
                    <span class="font-medium">{{ $order->getCustomerName() }}</span>
                </div>
                @if($order->getCustomerContact() && $order->getCustomerContact() !== 'N/A')
                    <div class="flex justify-between mt-1">
                        <span class="text-gray-600">Contact:</span>
                        <span>{{ $order->getCustomerContact() }}</span>
                    </div>
                @endif
                @if($order->rider)
                    <div class="flex justify-between mt-1">
                        <span class="text-gray-600">Rider:</span>
                        <span>{{ $order->rider->full_name }}</span>
                    </div>
                @endif
            </div>

            {{-- Items --}}
            <div class="border-t border-dashed border-gray-300 pt-3 mb-3">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-gray-300">
                            <th class="text-left pb-2">Item</th>
                            <th class="text-center pb-2">Qty</th>
                            <th class="text-right pb-2">Price</th>
                            <th class="text-right pb-2">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2">Water Refill (5-gal)</td>
                            <td class="text-center py-2">{{ $order->quantity }}</td>
                            <td class="text-right py-2">₱{{ number_format($order->unit_price, 2) }}</td>
                            <td class="text-right py-2">₱{{ number_format($order->quantity * $order->unit_price, 2) }}</td>
                        </tr>
                        @if($order->delivery_fee > 0)
                            <tr>
                                <td class="py-2">Delivery Fee</td>
                                <td class="text-center py-2">1</td>
                                <td class="text-right py-2">₱{{ number_format($order->delivery_fee, 2) }}</td>
                                <td class="text-right py-2">₱{{ number_format($order->delivery_fee, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Total --}}
            <div class="border-t border-dashed border-gray-300 pt-3 mb-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold">TOTAL:</span>
                    <span class="text-lg font-bold text-blue-700">₱{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            {{-- Payment Status --}}
            <div class="border-t border-dashed border-gray-300 pt-3 mb-3">
                @if($order->isPaid())
                    <div class="text-center">
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 font-bold rounded text-xs">✓ PAID</span>
                    </div>
                    <div class="text-xs mt-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Method:</span>
                            <span>{{ $order->getPaymentMethodLabel() }}</span>
                        </div>
                        @if($order->payment_reference)
                            <div class="flex justify-between mt-1">
                                <span class="text-gray-600">Reference:</span>
                                <span>{{ $order->payment_reference }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between mt-1">
                            <span class="text-gray-600">Paid at:</span>
                            <span>{{ $order->paid_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <span class="inline-block px-3 py-1 bg-orange-100 text-orange-800 font-bold rounded text-xs">⚠ UNPAID</span>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="border-t border-dashed border-gray-300 pt-4 text-center">
                <p class="text-xs text-gray-600">Thank you for your business!</p>
                <p class="text-xs text-gray-400 mt-1">Keep this receipt for your records</p>
            </div>
        </div>
    </div>

</body>
</html>
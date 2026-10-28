<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🗺️ Track Order #{{ $order->id }}
            </h2>
            <div class="flex items-center gap-3">
                <span id="live-indicator" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Live
                </span>
                <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="px-3 py-1 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium">
                    🧾 Receipt
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Status Banner --}}
            <div class="mb-6 p-6 rounded-2xl shadow-xl border-2
                @switch($order->status)
                    @case('pending') bg-gradient-to-r from-yellow-50 to-orange-50 border-yellow-300 @break
                    @case('assigned') bg-gradient-to-r from-blue-50 to-cyan-50 border-blue-300 @break
                    @case('on_delivery') bg-gradient-to-r from-purple-50 to-pink-50 border-purple-300 @break
                    @case('delivered') bg-gradient-to-r from-green-50 to-emerald-50 border-green-300 @break
                    @case('completed') bg-gradient-to-r from-emerald-50 to-teal-50 border-emerald-300 @break
                    @case('cancelled') bg-gradient-to-r from-red-50 to-rose-50 border-red-300 @break
                    @default bg-gray-50 border-gray-300
                @endswitch
            ">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-5xl mr-4">{{ $order->getStatusIcon() }}</span>
                        <div>
                            <p class="text-xs text-gray-600 uppercase tracking-wider font-medium">Current Status</p>
                            <p class="text-3xl font-bold text-gray-800">{{ $order->getStatusLabel() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Last Updated</p>
                        <p class="text-sm font-medium text-gray-700">{{ $order->updated_at->format('M d, Y') }}</p>
                        <p class="text-sm text-gray-500">{{ $order->updated_at->format('h:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Content: Timeline --}}
                <div class="lg:col-span-2">
                    {{-- Progress Timeline --}}
                    @if($order->status !== 'cancelled' && !($order->is_walk_in && $order->status === 'completed'))
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">📍 Order Progress</h3>

                            @php
                                $steps = $order->getTimelineSteps();
                                $currentIndex = $order->getCurrentStepIndex();
                                $stepLabels = [
                                    'pending' => ['Pending', 'Order placed, waiting for approval'],
                                    'assigned' => ['Assigned', 'Rider assigned to your order'],
                                    'on_delivery' => ['On Delivery', 'Rider is on the way to you'],
                                    'delivered' => ['Delivered', 'Order delivered successfully'],
                                    'completed' => ['Completed', 'Walk-in refill completed'],
                                    'cancelled' => ['Cancelled', 'Order was cancelled'],
                                ];
                            @endphp

                            <div class="flex items-center justify-between mb-8">
                                @foreach($steps as $index => $step)
                                    @php
                                        $isCompleted = $index < $currentIndex;
                                        $isCurrent = $index === $currentIndex;
                                        $isPending = $index > $currentIndex;
                                    @endphp
                                    <div class="flex flex-col items-center flex-1">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
                                            {{ $isCompleted ? 'bg-green-500 text-white' : '' }}
                                            {{ $isCurrent ? 'bg-blue-500 text-white ring-4 ring-blue-200 animate-pulse' : '' }}
                                            {{ $isPending ? 'bg-gray-200 text-gray-400' : '' }}
                                        ">
                                            @if($isCompleted)
                                                ✓
                                            @elseif($isCurrent)
                                                {{ $index + 1 }}
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </div>
                                        <p class="text-xs font-medium text-gray-700 mt-2 text-center">
                                            {{ $stepLabels[$step][0] }}
                                        </p>
                                    </div>
                                    @if(!$loop->last)
                                        <div class="flex-1 h-1 -mt-6 {{ $index < $currentIndex ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Activity Timeline --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Activity History</h3>

                        @if($order->statusLogs->isEmpty())
                            <p class="text-gray-500 text-sm">No activity recorded yet.</p>
                        @else
                            <div class="relative">
                                {{-- Vertical Line --}}
                                <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                                @foreach($order->statusLogs as $log)
                                    <div class="relative flex items-start mb-4 last:mb-0 pl-12">
                                        <div class="absolute left-0 w-10 h-10 rounded-full flex items-center justify-center text-lg
                                            @switch($log->status)
                                                @case('pending') bg-yellow-100 @break
                                                @case('assigned') bg-blue-100 @break
                                                @case('on_delivery') bg-purple-100 @break
                                                @case('delivered') bg-green-100 @break
                                                @case('completed') bg-emerald-100 @break
                                                @case('cancelled') bg-red-100 @break
                                                @default bg-gray-100
                                            @endswitch
                                        ">
                                            {{ $log->getStatusIcon() }}
                                        </div>
                                        <div class="flex-1 bg-gray-50 rounded-lg p-3">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="font-semibold text-gray-800 text-sm">{{ $log->getStatusLabel() }}</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $log->getChangedByLabel() }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs text-gray-500">{{ $log->created_at->format('M d, Y') }}</p>
                                                    <p class="text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</p>
                                                </div>
                                            </div>
                                            @if($log->notes)
                                                <p class="text-xs text-gray-600 mt-2 italic">💬 {{ $log->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Rider Info --}}
                    @if($order->rider)
                        <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl shadow-xl border border-cyan-200 p-5">
                            <h3 class="text-sm font-semibold text-cyan-800 uppercase tracking-wider mb-3">🛵 Your Rider</h3>
                            <div class="flex items-center mb-3">
                                <div class="w-12 h-12 bg-cyan-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    {{ strtoupper(substr($order->rider->name, 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-800">{{ $order->rider->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $order->rider->riderProfile->vehicle_type ?? 'Rider' }}
                                        @if($order->rider->riderProfile->plate_number)
                                            ({{ $order->rider->riderProfile->plate_number }})
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <p class="flex items-center">
                                    <span class="text-gray-500 mr-2">📞</span>
                                    <a href="tel:{{ $order->rider->contact_no }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $order->rider->contact_no }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Order Summary --}}
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">📦 Order Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Order #</span>
                                <span class="font-semibold">#{{ $order->id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Type</span>
                                <span class="font-medium">{{ $order->is_walk_in ? '🏪 Walk-in' : '🌐 Online' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Quantity</span>
                                <span class="font-medium">{{ $order->quantity }} gallon(s)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Address</span>
                                <span class="font-medium text-right text-xs max-w-[150px] truncate" title="{{ $order->delivery_address }}">
                                    {{ $order->delivery_address }}
                                </span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Unit × Qty</span>
                                    <span>₱{{ number_format($order->unit_price, 2) }} × {{ $order->quantity }}</span>
                                </div>
                                @if($order->delivery_fee > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Delivery</span>
                                        <span>₱{{ number_format($order->delivery_fee, 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between mt-2 pt-2 border-t border-gray-300">
                                    <span class="font-bold text-gray-800">Total</span>
                                    <span class="font-bold text-blue-700 text-lg">₱{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                            <div class="flex justify-between mt-3 pt-3 border-t border-gray-200">
                                <span class="text-gray-500">Payment</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getPaymentStatusBadgeColor() }}">
                                    {{ $order->getPaymentStatusLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Auto-refresh info --}}
                    <div class="bg-gray-50 rounded-2xl border border-gray-200 p-4 text-center">
                        <p class="text-xs text-gray-500">
                            🔄 Auto-refreshing every <strong>10 seconds</strong>
                        </p>
                        <p class="text-xs text-gray-400 mt-1" id="last-check">Checking...</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="mt-6 flex justify-center gap-3">
                <a href="javascript:history.back()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition shadow-md text-sm font-medium">
                    ← Back
                </a>
            </div>
        </div>
    </div>

    <script>
        const orderId = {{ $order->id }};
        const currentStatus = '{{ $order->status }}';
        const currentLogCount = {{ $order->statusLogs->count() }};
        const statusJsonUrl = '{{ route('orders.status-json', $order->id) }}';
        let isPolling = true;

        // Toast notification
        function showToast(message, icon) {
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-gradient-to-r from-blue-600 to-cyan-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transition-all duration-300';
            toast.style.opacity = '0';
            toast.style.transform = 'translate(-50%, -20px)';
            toast.innerHTML = `
                <span class="text-2xl">${icon || '🔔'}</span>
                <div>
                    <p class="font-bold text-sm">Status Updated!</p>
                    <p class="text-xs opacity-90">${message}</p>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translate(-50%, 0)';
            }, 50);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translate(-50%, -20px)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Beep sound
        function playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.frequency.value = 800;
                osc.type = 'sine';
                gain.gain.setValueAtTime(0.15, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            } catch (e) {
                console.warn('Audio not available:', e);
            }
        }

        // Polling
        async function checkStatus() {
            if (!isPolling) return;
            try {
                const res = await fetch(statusJsonUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                if (!res.ok) {
                    document.getElementById('last-check').textContent = '⚠️ Check failed';
                    return;
                }

                const data = await res.json();
                document.getElementById('last-check').textContent = '✓ Last check: ' + new Date().toLocaleTimeString();

                // Detect change
                if (data.status !== currentStatus || data.logs_count !== currentLogCount) {
                    isPolling = false;
                    document.getElementById('live-indicator').innerHTML =
                        '<span class="inline-block w-2 h-2 bg-orange-500 rounded-full mr-2 animate-pulse"></span>Updating...';

                    showToast(`${data.status_icon} ${data.status_label}`, data.status_icon);
                    playBeep();

                    setTimeout(() => window.location.reload(), 2500);
                }
            } catch (err) {
                console.error('Polling error:', err);
                document.getElementById('last-check').textContent = '⚠️ Connection error';
            }
        }

        // Poll every 10 seconds
        setInterval(checkStatus, 10000);

        // Check immediately after 2 seconds (page settled)
        setTimeout(checkStatus, 2000);

        // Pause polling when tab is hidden, resume when visible
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                isPolling = false;
            } else {
                isPolling = true;
                setTimeout(checkStatus, 500);
            }
        });
    </script>
</x-app-layout>
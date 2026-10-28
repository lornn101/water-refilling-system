<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📜 Admin Activity Log
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                👑 Owner Only
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-lg p-5 text-white">
                    <p class="text-blue-100 text-xs uppercase tracking-wider">📊 Total Actions Logged</p>
                    <p class="text-3xl font-bold mt-1">{{ $totalLogs }}</p>
                </div>
                <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl shadow-lg p-5 text-white">
                    <p class="text-emerald-100 text-xs uppercase tracking-wider">📅 Actions Today</p>
                    <p class="text-3xl font-bold mt-1">{{ $todayLogs }}</p>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg p-5 text-white">
                    <p class="text-purple-100 text-xs uppercase tracking-wider">📈 Last 7 Days</p>
                    <p class="text-3xl font-bold mt-1">{{ $weekLogs }}</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
                <form method="GET" action="{{ route('owner.activity-log') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Admin, user, or description..." class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                    </div>
                    <div class="w-[160px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Action Type</label>
                        <select name="action" class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                            <option value="">All Actions</option>
                            @foreach($actionTypes as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-[160px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Admin</label>
                        <select name="admin_id" class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                            <option value="">All Admins</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->admin_id }}" {{ request('admin_id') == $admin->admin_id ? 'selected' : '' }}>
                                    {{ $admin->admin_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        🔍 Filter
                    </button>
                    <a href="{{ route('owner.activity-log') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                        ↺ Reset
                    </a>
                </form>
            </div>

            {{-- Log Table --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                @if($logs->isEmpty())
                    <div class="p-12 text-center">
                        <div class="text-5xl mb-3">📭</div>
                        <p class="text-gray-500">No activity logs found.</p>
                        <p class="text-xs text-gray-400 mt-1">Actions will appear here as they occur.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">When</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target User</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($logs as $log)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <p class="font-medium text-gray-800">{{ $log->created_at->format('M d, Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ $log->created_at->format('h:i A') }}</p>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-700 font-bold text-xs">
                                                    {{ strtoupper(substr($log->admin_name, 0, 1)) }}
                                                </div>
                                                <span class="ml-2 font-medium text-gray-800">{{ $log->admin_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->getActionBadgeColor() }}">
                                                {{ $log->getActionIcon() }} {{ $log->getActionLabel() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">
                                            {{ $log->target_user_name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $log->description ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-400 font-mono">
                                            {{ $log->ip_address ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
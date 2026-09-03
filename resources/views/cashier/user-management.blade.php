<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Account Management') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                Cashier
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50/50 via-cyan-50/50 to-blue-100/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200 flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">×</button>
                </div>
            @endif

            {{-- Stats Dashboard --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-blue-500">
                    <p class="text-2xl font-bold text-blue-600">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-500">Total Users</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
                    <p class="text-xs text-gray-500">Pending</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-green-500">
                    <p class="text-2xl font-bold text-green-600">{{ $approvedCount }}</p>
                    <p class="text-xs text-gray-500">Approved</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-red-500">
                    <p class="text-2xl font-bold text-red-600">{{ $rejectedCount }}</p>
                    <p class="text-xs text-gray-500">Rejected</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-gray-500">
                    <p class="text-2xl font-bold text-gray-600">{{ $deletedCount }}</p>
                    <p class="text-xs text-gray-500">Deleted</p>
                </div>
            </div>

            {{-- Toolbar: Search, Filters, Actions --}}
            <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
                <form method="GET" action="{{ route('cashier.users') }}" class="flex flex-wrap gap-3 items-end">
                    {{-- Search --}}
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Name or email..." 
                               class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                    </div>

                    {{-- Role Filter --}}
                    <div class="w-[130px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Role</label>
                        <select name="role" class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                            <option value="">All</option>
                            <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="rider" {{ request('role') == 'rider' ? 'selected' : '' }}>Rider</option>
                            <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div class="w-[130px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    {{-- Deleted Filter --}}
                    <div class="w-[130px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Deleted</label>
                        <select name="deleted" class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                            <option value="">Active Only</option>
                            <option value="with" {{ request('deleted') == 'with' ? 'selected' : '' }}>With Deleted</option>
                            <option value="only" {{ request('deleted') == 'only' ? 'selected' : '' }}>Deleted Only</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                            🔍 Filter
                        </button>
                        <a href="{{ route('cashier.users') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                            ↺ Reset
                        </a>
                    </div>

                    {{-- Add Rider Button --}}
                    <div class="ml-auto">
                        <a href="{{ route('cashier.create-rider') }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition shadow-md text-sm font-medium inline-flex items-center">
                            ➕ Add Rider
                        </a>
                    </div>
                </form>
            </div>

            {{-- Users Table --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 transition {{ $user->trashed() ? 'bg-gray-100 opacity-60' : '' }}">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                        @if($user->trashed())
                                            <span class="ml-2 text-xs text-red-500">(Deleted)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $user->contact_no }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $user->role === 'cashier' ? 'bg-purple-100 text-purple-800' : '' }}
                                            {{ $user->role === 'rider' ? 'bg-cyan-100 text-cyan-800' : '' }}
                                            {{ $user->role === 'customer' ? 'bg-blue-100 text-blue-800' : '' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($user->trashed())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                Deleted
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $user->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $user->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $user->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium space-x-1">
                                        {{-- Approve/Reject (only for pending customers) --}}
                                        @if(!$user->trashed() && $user->status === 'pending' && $user->role === 'customer')
                                            <form action="{{ route('cashier.users.approve', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition text-xs">✅</button>
                                            </form>
                                            <form action="{{ route('cashier.users.reject', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition text-xs">❌</button>
                                            </form>
                                        @endif

                                        {{-- Edit --}}
                                        @if(!$user->trashed())
                                            <a href="{{ route('cashier.users.edit', $user->id) }}" class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs inline-block">✏️</a>
                                        @endif

                                        {{-- Delete / Restore / Force Delete --}}
                                        @if($user->trashed())
                                            <form action="{{ route('cashier.users.restore', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition text-xs">♻️</button>
                                            </form>
                                            <form action="{{ route('cashier.users.force-delete', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ Permanently delete this user? This cannot be undone!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 bg-red-800 text-white rounded hover:bg-red-900 transition text-xs">💀</button>
                                            </form>
                                        @else
                                            <form action="{{ route('cashier.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('🗑️ Delete this user? They can be restored later.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition text-xs">🗑️</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        No users found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
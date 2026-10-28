<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can view activity logs.');
        }

        $query = AdminActivityLog::with(['admin', 'targetUser']);

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Search by target or admin name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('admin_name', 'like', "%{$search}%")
                  ->orWhere('target_user_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Stats
        $totalLogs = AdminActivityLog::count();
        $todayLogs = AdminActivityLog::whereDate('created_at', today())->count();
        $weekLogs = AdminActivityLog::where('created_at', '>=', now()->subDays(7))->count();

        // Action types for filter dropdown
        $actionTypes = AdminActivityLog::select('action')->distinct()->pluck('action');

        // Admins who did actions (for filter dropdown)
        $admins = AdminActivityLog::select('admin_id', 'admin_name')
            ->distinct()
            ->whereNotNull('admin_id')
            ->orderBy('admin_name')
            ->get();

        return view('owner.activity-log', compact(
            'logs', 'totalLogs', 'todayLogs', 'weekLogs', 'actionTypes', 'admins'
        ));
    }
}
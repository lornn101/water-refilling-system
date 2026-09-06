<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RiderProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display user management dashboard with search, filter, and pagination.
     */
    public function index(Request $request)
    {
        // ✅ Only OWNER can access this
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can manage users.');
        }

        // Build the query
        $query = User::query();

        // 🔍 Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 🎯 Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // 🎯 Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🎯 Filter by deleted (soft delete)
        if ($request->filled('deleted')) {
            if ($request->deleted === 'only') {
                $query->onlyTrashed();
            } elseif ($request->deleted === 'with') {
                $query->withTrashed();
            }
        } else {
            // Default: only show non-deleted users
            $query->whereNull('deleted_at');
        }

        // Order by latest first
        $query->orderBy('created_at', 'desc');

        // 📄 Paginate (10 per page)
        $users = $query->paginate(10)->withQueryString();

        // 📊 Get counts for dashboard stats
        $totalUsers = User::count();
        $pendingCount = User::where('status', 'pending')->whereNull('deleted_at')->count();
        $approvedCount = User::where('status', 'approved')->whereNull('deleted_at')->count();
        $rejectedCount = User::where('status', 'rejected')->whereNull('deleted_at')->count();
        $deletedCount = User::onlyTrashed()->count();

        return view('cashier.user-management', compact(
            'users',
            'totalUsers',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'deletedCount'
        ));
    }

    /**
     * Approve a user account.
     */
    public function approve($id)
    {
        // ✅ Only OWNER can approve
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can approve users.');
        }

        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        return redirect()->route('cashier.users')->with('success', "✅ User '{$user->name}' has been approved successfully.");
    }

    /**
     * Reject a user account.
     */
    public function reject($id)
    {
        // ✅ Only OWNER can reject
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can reject users.');
        }

        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        return redirect()->route('cashier.users')->with('success', "❌ User '{$user->name}' has been rejected.");
    }

    /**
     * Show edit form for a user.
     */
    public function edit($id)
    {
        // ✅ Only OWNER can edit
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can edit users.');
        }

        $user = User::withTrashed()->findOrFail($id);
        return view('cashier.user-edit', compact('user'));
    }

    /**
     * Update a user's details.
     */
    public function update(Request $request, $id)
    {
        // ✅ Only OWNER can update
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can update users.');
        }

        $user = User::withTrashed()->findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'contact_no' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:customer,rider,cashier,owner'], // ✅ Added 'owner'
        ];

        // Only validate password if it's being changed
        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $request->validate($rules);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->contact_no = $request->contact_no;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('cashier.users')->with('success', "✏️ User '{$user->name}' has been updated successfully.");
    }

    /**
     * Soft delete a user.
     */
    public function destroy($id)
    {
        // ✅ Only OWNER can delete
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can delete users.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('cashier.users')->with('success', "🗑️ User '{$user->name}' has been deleted.");
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($id)
    {
        // ✅ Only OWNER can restore
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can restore users.');
        }

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('cashier.users')->with('success', "♻️ User '{$user->name}' has been restored.");
    }

    /**
     * Permanently delete a user (hard delete).
     */
    public function forceDelete($id)
    {
        // ✅ Only OWNER can force delete
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can permanently delete users.');
        }

        $user = User::onlyTrashed()->findOrFail($id);
        $userName = $user->name;
        $user->forceDelete();

        return redirect()->route('cashier.users')->with('success', "🗑️ User '{$userName}' has been permanently deleted.");
    }

    /**
     * Show the create rider form.
     */
    public function createRider()
    {
        // ✅ Only OWNER can create riders
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can create rider accounts.');
        }
        return view('cashier.create-rider');
    }

    /**
     * Store a new rider account.
     */
    public function storeRider(Request $request)
    {
        // ✅ Only OWNER can store riders
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can create rider accounts.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_no' => ['required', 'string', 'max:20'],
            'vehicle_type' => ['required', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'role' => 'rider',
            'status' => 'approved',
            'password' => Hash::make($request->password),
        ]);

        RiderProfile::create([
            'user_id' => $user->id,
            'vehicle_type' => $request->vehicle_type,
            'plate_number' => $request->plate_number,
            'availability_status' => 'available',
        ]);

        return redirect()->route('cashier.users')->with('success', "🛵 Rider '{$user->name}' created successfully!");
    }
}
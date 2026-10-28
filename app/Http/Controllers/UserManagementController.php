<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RiderProfile;
use App\Models\AdminActivityLog;
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
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can manage users.');
        }

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('deleted')) {
            if ($request->deleted === 'only') {
                $query->onlyTrashed();
            } elseif ($request->deleted === 'with') {
                $query->withTrashed();
            }
        } else {
            $query->whereNull('deleted_at');
        }

        $query->orderBy('created_at', 'desc');
        $users = $query->paginate(10)->withQueryString();

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
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        // ✅ Log activity
        AdminActivityLog::log('approved', $user, "Approved user account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "✅ User '{$user->name}' has been approved successfully.");
    }

    /**
     * Reject a user account.
     */
    public function reject($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        // ✅ Log activity
        AdminActivityLog::log('rejected', $user, "Rejected user account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "❌ User '{$user->name}' has been rejected.");
    }

    /**
     * Show edit form for a user.
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::withTrashed()->findOrFail($id);
        return view('cashier.user-edit', compact('user'));
    }

    /**
     * Update a user's details.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::withTrashed()->findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'contact_no' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:customer,rider,cashier,owner'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $request->validate($rules);

        $oldRole = $user->role;
        $oldName = $user->name;

        $user->name = $request->name;
        $user->email = $request->email;
        $user->contact_no = $request->contact_no;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // ✅ Log activity
        $description = $oldRole !== $user->role
            ? "Changed role from '{$oldRole}' to '{$user->role}'"
            : "Updated user account details ({$user->email})";
        AdminActivityLog::log('edited', $user, $description);

        return redirect()->route('cashier.users')->with('success', "✏️ User '{$user->name}' has been updated successfully.");
    }

    /**
     * Soft delete a user.
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::findOrFail($id);
        $user->delete();

        // ✅ Log activity
        AdminActivityLog::log('deleted', $user, "Soft-deleted user account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "🗑️ User '{$user->name}' has been deleted.");
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        // ✅ Log activity
        AdminActivityLog::log('restored', $user, "Restored deleted user account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "♻️ User '{$user->name}' has been restored.");
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::onlyTrashed()->findOrFail($id);
        $userName = $user->name;
        $userEmail = $user->email;

        // ✅ Log BEFORE force delete (so user exists at log time)
        AdminActivityLog::log('force_deleted', $user, "Permanently deleted user account ({$userEmail})");

        $user->forceDelete();

        return redirect()->route('cashier.users')->with('success', "🗑️ User '{$userName}' has been permanently deleted.");
    }

    /**
     * Show the create rider form.
     */
    public function createRider()
    {
        if (Auth::user()->role !== 'owner') abort(403);
        return view('cashier.create-rider');
    }

    /**
     * Store a new rider account.
     */
    public function storeRider(Request $request)
    {
        if (Auth::user()->role !== 'owner') abort(403);

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

        // ✅ Log activity
        AdminActivityLog::log('created_rider', $user, "Created new rider account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "🛵 Rider '{$user->name}' created successfully!");
    }
}
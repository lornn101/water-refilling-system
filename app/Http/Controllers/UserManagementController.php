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
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can manage users.');
        }

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"]);
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
            'users', 'totalUsers', 'pendingCount', 'approvedCount', 'rejectedCount', 'deletedCount'
        ));
    }

    public function approve($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::findOrFail($id);
        $user->status = 'approved';
        $user->save();

        AdminActivityLog::log('approved', $user, "Approved user account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "✅ User '{$user->full_name}' has been approved successfully.");
    }

    public function reject($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::findOrFail($id);
        $user->status = 'rejected';
        $user->save();

        AdminActivityLog::log('rejected', $user, "Rejected user account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "❌ User '{$user->full_name}' has been rejected.");
    }

    public function edit($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::withTrashed()->findOrFail($id);
        return view('cashier.user-edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::withTrashed()->findOrFail($id);

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'contact_no' => ['required', 'string', 'max:20'],
            'role' => ['required', 'in:customer,rider,cashier,owner'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['string', 'min:8', 'confirmed'];
        }

        $request->validate($rules);

        $oldRole = $user->role;

        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->contact_no = $request->contact_no;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $description = $oldRole !== $user->role
            ? "Changed role from '{$oldRole}' to '{$user->role}'"
            : "Updated user account details ({$user->email})";
        AdminActivityLog::log('edited', $user, $description);

        return redirect()->route('cashier.users')->with('success', "✏️ User '{$user->full_name}' has been updated successfully.");
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::findOrFail($id);
        $user->delete();

        AdminActivityLog::log('deleted', $user, "Soft-deleted user account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "🗑️ User '{$user->full_name}' has been deleted.");
    }

    public function restore($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        AdminActivityLog::log('restored', $user, "Restored deleted user account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "♻️ User '{$user->full_name}' has been restored.");
    }

    public function forceDelete($id)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $user = User::onlyTrashed()->findOrFail($id);
        $fullName = $user->full_name;
        $userEmail = $user->email;

        AdminActivityLog::log('force_deleted', $user, "Permanently deleted user account ({$userEmail})");

        $user->forceDelete();

        return redirect()->route('cashier.users')->with('success', "🗑️ User '{$fullName}' has been permanently deleted.");
    }

    public function createRider()
    {
        if (Auth::user()->role !== 'owner') abort(403);
        return view('cashier.create-rider');
    }

    public function storeRider(Request $request)
    {
        if (Auth::user()->role !== 'owner') abort(403);

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_no' => ['required', 'string', 'max:20'],
            'vehicle_type' => ['required', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
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

        AdminActivityLog::log('created_rider', $user, "Created new rider account ({$user->email})");

        return redirect()->route('cashier.users')->with('success', "🛵 Rider '{$user->full_name}' created successfully!");
    }
}
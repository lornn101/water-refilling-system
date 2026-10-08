<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // ============================================================
    // HELPERS
    // ============================================================
    private function isOwner()    { return Auth::check() && Auth::user()->role === 'owner'; }
    private function isCashier()  { return Auth::check() && Auth::user()->role === 'cashier'; }
    private function isRider()    { return Auth::check() && Auth::user()->role === 'rider'; }
    private function isCustomer() { return Auth::check() && Auth::user()->role === 'customer'; }

    private function canManageOrders()
    {
        return Auth::check() && in_array(Auth::user()->role, ['owner', 'cashier']);
    }

    // ============================================================
    // 🟢 CUSTOMER: Place Order
    // ============================================================
    public function create()
    {
        if (!$this->isCustomer()) abort(403, 'Only customers can place orders.');
        $user = Auth::user();
        return view('customer.place-order', compact('user'));
    }

    public function store(Request $request)
{
    if (!$this->isCustomer()) abort(403, 'Only customers can place orders.');

    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        'delivery_address' => ['required', 'string', 'max:500'],
        'delivery_notes' => ['nullable', 'string', 'max:500'],
        'contact_number' => ['required', 'string', 'max:20'],
        'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $order = Order::create([
        'customer_id' => $user->id,
        'is_walk_in' => false,        // ✅ Explicitly false
        'quantity' => $request->quantity,
        'delivery_address' => $request->delivery_address,
        'delivery_notes' => $request->delivery_notes,
        'contact_number' => $request->contact_number,
        'status' => 'pending',
        'delivery_date' => $request->delivery_date ?? now()->addDay(),
    ]);

    return redirect()->route('customer.orders')
        ->with('success', '✅ Order #' . $order->id . ' placed successfully! Waiting for approval.');
}

    // ============================================================
    // 🟢 CUSTOMER: Order History
    // ============================================================
    public function customerOrders()
    {
        if (!$this->isCustomer()) abort(403, 'Unauthorized access.');

        $orders = Order::where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    // ============================================================
    // 🟢 CUSTOMER: Edit Pending Order
    // ============================================================
    public function customerEdit($id)
{
    if (!$this->isCustomer()) abort(403, 'Unauthorized access.');

    $order = Order::where('customer_id', Auth::id())
        ->where('status', 'pending')
        ->findOrFail($id);

    return view('customer.edit-order', compact('order'));
}

    public function customerUpdate(Request $request, $id)
{
    if (!$this->isCustomer()) abort(403, 'Unauthorized access.');

    $order = Order::where('customer_id', Auth::id())
        ->where('status', 'pending')
        ->findOrFail($id);

    $validator = Validator::make($request->all(), [
        'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        'delivery_address' => ['required', 'string', 'max:500'],
        'delivery_notes' => ['nullable', 'string', 'max:500'],
        'contact_number' => ['required', 'string', 'max:20'],
        'delivery_date' => ['nullable', 'date', 'after_or_equal:today'],
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $order->update([
        'quantity' => $request->quantity,
        'delivery_address' => $request->delivery_address,
        'delivery_notes' => $request->delivery_notes,
        'contact_number' => $request->contact_number,
        'delivery_date' => $request->delivery_date,
    ]);

    return redirect()->route('customer.orders')
        ->with('success', '✏️ Order #' . $order->id . ' updated successfully!');
}

    // ============================================================
    // 🟢 CUSTOMER: Cancel Pending Order
    // ============================================================
    public function customerCancel($id)
{
    if (!$this->isCustomer()) abort(403, 'Unauthorized access.');

    $order = Order::where('customer_id', Auth::id())
        ->where('status', 'pending')
        ->findOrFail($id);

    $order->status = 'cancelled';
    $order->save();

    return redirect()->route('customer.orders')
        ->with('success', '❌ Order #' . $order->id . ' cancelled successfully.');
}

    // ============================================================
    // 🟢 OWNER / CASHIER: List All Orders
    // ============================================================
    public function ownerOrders(Request $request)
    {
        if (!$this->canManageOrders()) abort(403);

        $query = Order::with(['customer', 'rider']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('walk_in_customer_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            if ($request->type === 'walk_in') {
                $query->where('is_walk_in', true);
            } elseif ($request->type === 'online') {
                $query->where('is_walk_in', false);
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $riders = User::where('role', 'rider')->where('status', 'approved')->get();

        // Stats
        $totalOrders = Order::count();
        $pendingCount = Order::where('status', 'pending')->count();
        $inProgressCount = Order::whereIn('status', ['assigned', 'on_delivery'])->count();
        $deliveredCount = Order::where('status', 'delivered')->count();
        $completedCount = Order::where('status', 'completed')->count();
        $cancelledCount = Order::where('status', 'cancelled')->count();

        return view('owner.orders', compact(
            'orders', 'riders', 'totalOrders',
            'pendingCount', 'inProgressCount', 'deliveredCount',
            'completedCount', 'cancelledCount'
        ));
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: View Order Detail
    // ============================================================
    public function show($id)
    {
        if (!$this->canManageOrders()) abort(403);

        $order = Order::with(['customer', 'rider'])->findOrFail($id);
        $riders = User::where('role', 'rider')->where('status', 'approved')->get();

        return view('owner.order-detail', compact('order', 'riders'));
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: Assign Rider
    // ============================================================
    public function assignRider(Request $request, $id)
    {
        if (!$this->canManageOrders()) abort(403);

        $request->validate([
            'rider_id' => ['required', 'exists:users,id,role,rider'],
        ]);

        $order = Order::findOrFail($id);

        if (!in_array($order->status, ['pending', 'assigned'])) {
            return redirect()->back()->with('error', '❌ This order cannot be assigned at this stage.');
        }

        $order->rider_id = $request->rider_id;
        $order->status = 'assigned';
        $order->assigned_at = now();
        $order->save();

        $riderName = User::find($request->rider_id)->name;

        return redirect()->back()->with('success', "✅ Order #{$order->id} assigned to '{$riderName}'.");
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: Update Status
    // ============================================================
    public function updateStatus(Request $request, $id)
    {
        if (!$this->canManageOrders()) abort(403);

        $request->validate([
            'status' => ['required', 'in:pending,assigned,on_delivery,delivered,cancelled,completed'],
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->status = $request->status;

        if ($request->status === 'delivered') {
            $order->delivered_at = now();
        }

        $order->save();

        return redirect()->back()
            ->with('success', "✅ Order #{$order->id} status updated from '{$oldStatus}' to '{$request->status}'.");
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: Walk-in Order Form
    // ============================================================
    public function walkInCreate()
    {
        if (!$this->canManageOrders()) abort(403);
        return view('owner.walk-in-order');
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: Store Walk-in Order
    // ============================================================
    public function walkInStore(Request $request)
    {
        if (!$this->canManageOrders()) abort(403);

        $validator = Validator::make($request->all(), [
            'walk_in_customer_name' => ['required', 'string', 'max:255'],
            'walk_in_contact' => ['nullable', 'string', 'max:20'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'order_type' => ['required', 'in:refill,delivery'],
            'delivery_address' => ['required_if:order_type,delivery', 'nullable', 'string', 'max:500'],
            'delivery_notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $isRefill = $request->order_type === 'refill';

        $order = Order::create([
            'customer_id' => null,
            'is_walk_in' => true,
            'walk_in_customer_name' => $request->walk_in_customer_name,
            'walk_in_contact' => $request->walk_in_contact,
            'quantity' => $request->quantity,
            'delivery_address' => $isRefill ? 'Walk-in Refill at Station' : $request->delivery_address,
            'delivery_notes' => $request->delivery_notes,
            'contact_number' => $request->walk_in_contact ?? 'N/A',
            'status' => $isRefill ? 'completed' : 'pending',
            'delivered_at' => $isRefill ? now() : null,
        ]);

        $message = $isRefill
            ? "🏪 Walk-in refill for '{$order->walk_in_customer_name}' recorded! (Order #{$order->id})"
            : "✅ Delivery request for '{$order->walk_in_customer_name}' created! Assign a rider. (Order #{$order->id})";

        return redirect()->route('owner.orders')->with('success', $message);
    }

    // ============================================================
    // 🟢 RIDER: View Assigned Orders
    // ============================================================
    public function riderOrders()
    {
        if (!$this->isRider()) abort(403);

        $assignedOrders = Order::where('rider_id', Auth::id())
            ->whereIn('status', ['assigned', 'on_delivery'])
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->get();

        $completedOrders = Order::where('rider_id', Auth::id())
            ->where('status', 'delivered')
            ->with('customer')
            ->orderBy('delivered_at', 'desc')
            ->paginate(10);

        $stats = [
            'assigned' => $assignedOrders->where('status', 'assigned')->count(),
            'on_delivery' => $assignedOrders->where('status', 'on_delivery')->count(),
            'completed' => $completedOrders->total(),
        ];

        return view('rider.orders', compact('assignedOrders', 'completedOrders', 'stats'));
    }

    public function markDelivered($id)
    {
        if (!$this->isRider()) abort(403);

        $order = Order::where('rider_id', Auth::id())
            ->whereIn('status', ['assigned', 'on_delivery'])
            ->findOrFail($id);

        $order->status = 'delivered';
        $order->delivered_at = now();
        $order->save();

        return redirect()->route('rider.orders')
            ->with('success', '✅ Order #' . $order->id . ' marked as delivered!');
    }

    public function markOnDelivery($id)
    {
        if (!$this->isRider()) abort(403);

        $order = Order::where('rider_id', Auth::id())
            ->where('status', 'assigned')
            ->findOrFail($id);

        $order->status = 'on_delivery';
        $order->save();

        return redirect()->route('rider.orders')
            ->with('success', '🚚 Order #' . $order->id . ' is now out for delivery!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Helper method to check if user is owner.
     */
    private function isOwner()
    {
        return Auth::check() && Auth::user()->role === 'owner';
    }

    /**
     * Helper method to check if user is cashier.
     */
    private function isCashier()
    {
        return Auth::check() && Auth::user()->role === 'cashier';
    }

    /**
     * Helper method to check if user is rider.
     */
    private function isRider()
    {
        return Auth::check() && Auth::user()->role === 'rider';
    }

    /**
     * Helper method to check if user is customer.
     */
    private function isCustomer()
    {
        return Auth::check() && Auth::user()->role === 'customer';
    }

    /**
     * Helper method to check if user has order management access (owner or cashier).
     */
    private function canManageOrders()
    {
        return Auth::check() && (Auth::user()->role === 'owner' || Auth::user()->role === 'cashier');
    }

    // ============================================================
    // 🟢 CUSTOMER: Place Order
    // ============================================================
    public function create()
    {
        if (!$this->isCustomer()) {
            abort(403, 'Only customers can place orders.');
        }

        $user = Auth::user();
        return view('customer.place-order', compact('user'));
    }

    public function store(Request $request)
    {
        if (!$this->isCustomer()) {
            abort(403, 'Only customers can place orders.');
        }

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
            'quantity' => $request->quantity,
            'delivery_address' => $request->delivery_address,
            'delivery_notes' => $request->delivery_notes,
            'contact_number' => $request->contact_number,
            'status' => 'pending',
            'delivery_date' => $request->delivery_date ?? now()->addDay(),
        ]);

        return redirect()->route('customer.orders')->with('success', '✅ Order placed successfully! Your order is pending approval.');
    }

    // ============================================================
    // 🟢 CUSTOMER: View Orders History
    // ============================================================
    public function customerOrders()
    {
        if (!$this->isCustomer()) {
            abort(403, 'Unauthorized access.');
        }

        $user = Auth::user();
        $orders = Order::where('customer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: View All Orders
    // ============================================================
    public function ownerOrders(Request $request)
    {
        if (!$this->canManageOrders()) {
            abort(403, 'Only the system owner and cashiers can view orders.');
        }

        $query = Order::with(['customer', 'rider']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by customer name or order ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get all riders for assignment dropdown
        $riders = User::where('role', 'rider')->where('status', 'approved')->get();

        // Get counts for stats
        $pendingCount = Order::where('status', 'pending')->count();
        $assignedCount = Order::where('status', 'assigned')->count();
        $onDeliveryCount = Order::where('status', 'on_delivery')->count();
        $deliveredCount = Order::where('status', 'delivered')->count();
        $cancelledCount = Order::where('status', 'cancelled')->count();
        $totalOrders = Order::count();

        return view('owner.orders', compact(
            'orders',
            'riders',
            'pendingCount',
            'assignedCount',
            'onDeliveryCount',
            'deliveredCount',
            'cancelledCount',
            'totalOrders'
        ));
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: Assign Rider to Order
    // ============================================================
    public function assignRider(Request $request, $id)
    {
        if (!$this->canManageOrders()) {
            abort(403, 'Only the system owner and cashiers can assign riders.');
        }

        $request->validate([
            'rider_id' => ['required', 'exists:users,id,role,rider'],
        ]);

        $order = Order::findOrFail($id);

        if ($order->status !== 'pending' && $order->status !== 'assigned') {
            return redirect()->back()->with('error', '❌ This order cannot be assigned at this stage.');
        }

        $order->rider_id = $request->rider_id;
        $order->status = 'assigned';
        $order->assigned_at = now();
        $order->save();

        $riderName = User::find($request->rider_id)->name;

        return redirect()->back()->with('success', "✅ Order #{$order->id} assigned to rider '{$riderName}' successfully!");
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: Update Order Status
    // ============================================================
    public function updateStatus(Request $request, $id)
    {
        if (!$this->canManageOrders()) {
            abort(403, 'Only the system owner and cashiers can update order status.');
        }

        $request->validate([
            'status' => ['required', 'in:pending,assigned,on_delivery,delivered,cancelled'],
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->status = $request->status;

        if ($request->status === 'delivered') {
            $order->delivered_at = now();
        }

        $order->save();

        return redirect()->back()->with('success', "✅ Order #{$order->id} status updated from '{$oldStatus}' to '{$request->status}'.");
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: View Single Order Details
    // ============================================================
    public function show($id)
    {
        if (!$this->canManageOrders()) {
            abort(403, 'Unauthorized access.');
        }

        $order = Order::with(['customer', 'rider'])->findOrFail($id);
        $riders = User::where('role', 'rider')->where('status', 'approved')->get();

        return view('owner.order-detail', compact('order', 'riders'));
    }

    // ============================================================
    // 🟢 RIDER: View Assigned Orders
    // ============================================================
    public function riderOrders()
    {
        if (!$this->isRider()) {
            abort(403, 'Only riders can view assigned orders.');
        }

        $user = Auth::user();

        $assignedOrders = Order::where('rider_id', $user->id)
            ->whereIn('status', ['assigned', 'on_delivery'])
            ->orderBy('created_at', 'desc')
            ->get();

        $completedOrders = Order::where('rider_id', $user->id)
            ->where('status', 'delivered')
            ->orderBy('delivered_at', 'desc')
            ->paginate(10);

        $stats = [
            'assigned' => $assignedOrders->where('status', 'assigned')->count(),
            'on_delivery' => $assignedOrders->where('status', 'on_delivery')->count(),
            'completed' => $completedOrders->total(),
        ];

        return view('rider.orders', compact('assignedOrders', 'completedOrders', 'stats'));
    }

    // ============================================================
    // 🟢 RIDER: Mark Order as Delivered
    // ============================================================
    public function markDelivered($id)
    {
        if (!$this->isRider()) {
            abort(403, 'Only riders can mark orders as delivered.');
        }

        $order = Order::where('rider_id', Auth::id())
            ->whereIn('status', ['assigned', 'on_delivery'])
            ->findOrFail($id);

        $order->status = 'delivered';
        $order->delivered_at = now();
        $order->save();

        return redirect()->route('rider.orders')->with('success', '✅ Order #' . $order->id . ' marked as delivered!');
    }

    // ============================================================
    // 🟢 RIDER: Mark Order as On Delivery
    // ============================================================
    public function markOnDelivery($id)
    {
        if (!$this->isRider()) {
            abort(403, 'Only riders can update delivery status.');
        }

        $order = Order::where('rider_id', Auth::id())
            ->where('status', 'assigned')
            ->findOrFail($id);

        $order->status = 'on_delivery';
        $order->save();

        return redirect()->route('rider.orders')->with('success', '🚚 Order #' . $order->id . ' is now out for delivery!');
    }
}
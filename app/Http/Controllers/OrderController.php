<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Setting;
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

    private function canTrackOrder($order)
    {
        if (!Auth::check()) return false;
        $user = Auth::user();

        if (in_array($user->role, ['owner', 'cashier'])) return true;
        if ($user->role === 'customer' && $order->customer_id === $user->id) return true;
        if ($user->role === 'rider' && $order->rider_id === $user->id) return true;

        return false;
    }

    private function getPricePerGallon() { return Setting::getDecimal('price_per_gallon', 25); }
    private function getDeliveryFee()    { return Setting::getDecimal('delivery_fee', 10); }

    // ============================================================
    // 🟢 CUSTOMER: Place Order
    // ============================================================
    public function create()
    {
        if (!$this->isCustomer()) abort(403, 'Only customers can place orders.');

        $user = Auth::user();
        $pricePerGallon = $this->getPricePerGallon();
        $deliveryFee = $this->getDeliveryFee();

        return view('customer.place-order', compact('user', 'pricePerGallon', 'deliveryFee'));
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

        $unitPrice = $this->getPricePerGallon();
        $deliveryFee = $this->getDeliveryFee();
        $totalAmount = ($request->quantity * $unitPrice) + $deliveryFee;

        $order = Order::create([
            'customer_id' => $user->id,
            'is_walk_in' => false,
            'quantity' => $request->quantity,
            'unit_price' => $unitPrice,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $totalAmount,
            'delivery_address' => $request->delivery_address,
            'delivery_notes' => $request->delivery_notes,
            'contact_number' => $request->contact_number,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'delivery_date' => $request->delivery_date ?? now()->addDay(),
        ]);

        // ✅ A7: Log initial status
        $order->logStatusChange(null, 'pending', $user->id, 'Order placed by customer');

        return redirect()->route('customer.orders')
            ->with('success', '✅ Order #' . $order->id . ' placed successfully! Total: ₱' . number_format($totalAmount, 2));
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

        $newTotal = ($request->quantity * $order->unit_price) + $order->delivery_fee;

        $order->update([
            'quantity' => $request->quantity,
            'total_amount' => $newTotal,
            'delivery_address' => $request->delivery_address,
            'delivery_notes' => $request->delivery_notes,
            'contact_number' => $request->contact_number,
            'delivery_date' => $request->delivery_date,
        ]);

        return redirect()->route('customer.orders')
            ->with('success', '✏️ Order #' . $order->id . ' updated successfully! New total: ₱' . number_format($newTotal, 2));
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

        $oldStatus = $order->status;
        $order->status = 'cancelled';
        $order->save();

        $order->logStatusChange($oldStatus, 'cancelled', Auth::id(), 'Order cancelled by customer');

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

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('walk_in_customer_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
    $q2->where('first_name', 'like', "%{$search}%")
       ->orWhere('last_name', 'like', "%{$search}%")
       ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"]);
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

        $totalOrders = Order::count();
        $pendingCount = Order::where('status', 'pending')->count();
        $inProgressCount = Order::whereIn('status', ['assigned', 'on_delivery'])->count();
        $deliveredCount = Order::where('status', 'delivered')->count();
        $completedCount = Order::where('status', 'completed')->count();
        $cancelledCount = Order::where('status', 'cancelled')->count();

        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $unpaidAmount = Order::where('payment_status', 'unpaid')->whereIn('status', ['delivered', 'completed'])->sum('total_amount');

        return view('owner.orders', compact(
            'orders', 'riders', 'totalOrders',
            'pendingCount', 'inProgressCount', 'deliveredCount',
            'completedCount', 'cancelledCount',
            'totalRevenue', 'unpaidAmount'
        ));
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: View Order Detail
    // ============================================================
    public function show($id)
    {
        if (!$this->canManageOrders()) abort(403);

        $order = Order::with(['customer', 'rider', 'paidBy', 'statusLogs.changedBy'])->findOrFail($id);
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

        $oldStatus = $order->status;
        $oldRiderId = $order->rider_id;

        $order->rider_id = $request->rider_id;
        $order->status = 'assigned';
        $order->assigned_at = now();
        $order->save();

        $riderName = User::find($request->rider_id)->full_name;
        $notes = $oldRiderId && $oldRiderId != $request->rider_id
            ? "Rider reassigned to {$riderName}"
            : "Assigned to rider: {$riderName}";
        $order->logStatusChange($oldStatus, 'assigned', Auth::id(), $notes);

        return redirect()->back()->with('success', "✅ Order #{$order->id} assigned to '{$riderName}'.");
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: Update Order Status
    // ============================================================
    public function updateStatus(Request $request, $id)
    {
        if (!$this->canManageOrders()) abort(403);

        $request->validate([
            'status' => ['required', 'in:pending,assigned,on_delivery,delivered,cancelled,completed'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::findOrFail($id);
        $oldStatus = $order->status;

        if ($oldStatus === $request->status) {
            return redirect()->back()->with('error', '⚠️ Order is already in that status.');
        }

        $order->status = $request->status;

        if ($request->status === 'delivered') {
            $order->delivered_at = now();
        }

        $order->save();

        $order->logStatusChange($oldStatus, $request->status, Auth::id(), $request->notes);

        return redirect()->back()
            ->with('success', "✅ Order #{$order->id} status updated from '{$oldStatus}' to '{$request->status}'.");
    }

    // ============================================================
    // 💰 MARK AS PAID
    // ============================================================
    public function markPaid(Request $request, $id)
    {
        if (!$this->canManageOrders() && !$this->isRider()) {
            abort(403, 'Only owner, cashier, or rider can record payments.');
        }

        $request->validate([
            'payment_method' => ['required', 'in:cash,gcash,bank_transfer'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $order = Order::findOrFail($id);

        if ($order->isPaid()) {
            return redirect()->back()->with('error', '⚠️ This order is already marked as paid.');
        }

        $order->payment_status = 'paid';
        $order->payment_method = $request->payment_method;
        $order->payment_reference = $request->payment_reference;
        $order->paid_at = now();
        $order->paid_by = Auth::id();
        $order->save();

        return redirect()->back()->with('success', '💰 Payment recorded! ₱' . number_format($order->total_amount, 2) . ' received.');
    }

    // ============================================================
    // 💰 UNMARK PAID (Owner only)
    // ============================================================
    public function unmarkPaid($id)
    {
        if (!$this->isOwner()) abort(403, 'Only the owner can revert payment.');

        $order = Order::findOrFail($id);

        if (!$order->isPaid()) {
            return redirect()->back()->with('error', '⚠️ This order is not marked as paid.');
        }

        $order->payment_status = 'unpaid';
        $order->payment_method = 'none';
        $order->payment_reference = null;
        $order->paid_at = null;
        $order->paid_by = null;
        $order->save();

        return redirect()->back()->with('success', '↩️ Payment reverted. Order is now unpaid.');
    }

    // ============================================================
    // 🧾 RECEIPT
    // ============================================================
    public function receipt($id)
    {
        if (!Auth::check()) abort(403);

        $order = Order::with(['customer', 'rider', 'paidBy'])->findOrFail($id);

        if (!$this->canTrackOrder($order)) abort(403);

        return view('receipts.show', compact('order'));
    }

    // ============================================================
    // 🆕 A7: ORDER TRACKING (Customer/Rider/Owner)
    // ============================================================
    public function trackOrder($id)
    {
        if (!Auth::check()) abort(403);

        $order = Order::with(['customer', 'rider.riderProfile', 'statusLogs.changedBy', 'paidBy'])->findOrFail($id);

        if (!$this->canTrackOrder($order)) abort(403, 'You are not authorized to track this order.');

        return view('orders.track', compact('order'));
    }

    // ============================================================
    // 🆕 A7: ORDER STATUS JSON API (for polling)
    // ============================================================
    public function statusJson($id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $order = Order::with(['rider.riderProfile', 'statusLogs'])->findOrFail($id);

        if (!$this->canTrackOrder($order)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json([
            'status' => $order->status,
            'status_label' => $order->getStatusLabel(),
            'status_icon' => $order->getStatusIcon(),
            'logs_count' => $order->statusLogs->count(),
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->getPaymentStatusLabel(),
            'rider' => $order->rider ? [
            'name' => $order->rider->full_name,
                'contact' => $order->rider->contact_no,
                'vehicle' => $order->rider->riderProfile->vehicle_type ?? null,
                'plate' => $order->rider->riderProfile->plate_number ?? null,
            ] : null,
            'last_updated' => $order->updated_at->toIso8601String(),
        ]);
    }

    // ============================================================
    // 🟢 OWNER / CASHIER: Walk-in Order Form
    // ============================================================
    public function walkInCreate()
    {
        if (!$this->canManageOrders()) abort(403);

        $pricePerGallon = $this->getPricePerGallon();
        $deliveryFee = $this->getDeliveryFee();

        return view('owner.walk-in-order', compact('pricePerGallon', 'deliveryFee'));
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
            'mark_paid' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $isRefill = $request->order_type === 'refill';
        $unitPrice = $this->getPricePerGallon();
        $deliveryFee = $isRefill ? 0 : $this->getDeliveryFee();
        $totalAmount = ($request->quantity * $unitPrice) + $deliveryFee;

        $isPaid = !$isRefill ? false : ($request->mark_paid ? true : false);

        $order = Order::create([
            'customer_id' => null,
            'is_walk_in' => true,
            'walk_in_customer_name' => $request->walk_in_customer_name,
            'walk_in_contact' => $request->walk_in_contact,
            'quantity' => $request->quantity,
            'unit_price' => $unitPrice,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $totalAmount,
            'delivery_address' => $isRefill ? 'Walk-in Refill at Station' : $request->delivery_address,
            'delivery_notes' => $request->delivery_notes,
            'contact_number' => $request->walk_in_contact ?? 'N/A',
            'status' => $isRefill ? 'completed' : 'pending',
            'payment_status' => $isPaid ? 'paid' : 'unpaid',
            'payment_method' => $isPaid ? 'cash' : 'none',
            'paid_at' => $isPaid ? now() : null,
            'paid_by' => $isPaid ? Auth::id() : null,
            'delivered_at' => $isRefill ? now() : null,
        ]);

        // ✅ A7: Log initial status
        if ($isRefill) {
            $order->logStatusChange(null, 'completed', Auth::id(), 'Walk-in refill completed at station');
        } else {
            $order->logStatusChange(null, 'pending', Auth::id(), 'Walk-in delivery request created');
        }

        $message = $isRefill
            ? "🏪 Walk-in refill for '{$order->walk_in_customer_name}' — ₱" . number_format($totalAmount, 2) . " (" . ($isPaid ? 'PAID' : 'UNPAID') . ")"
            : "✅ Delivery request for '{$order->walk_in_customer_name}' — ₱" . number_format($totalAmount, 2) . ". Assign a rider.";

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

        $oldStatus = $order->status;
        $order->status = 'delivered';
        $order->delivered_at = now();
        $order->save();

        $order->logStatusChange($oldStatus, 'delivered', Auth::id(), 'Delivered to customer by rider');

        return redirect()->route('rider.orders')
            ->with('success', '✅ Order #' . $order->id . ' marked as delivered!');
    }

    public function markOnDelivery($id)
    {
        if (!$this->isRider()) abort(403);

        $order = Order::where('rider_id', Auth::id())
            ->where('status', 'assigned')
            ->findOrFail($id);

        $oldStatus = $order->status;
        $order->status = 'on_delivery';
        $order->save();

        $order->logStatusChange($oldStatus, 'on_delivery', Auth::id(), 'Rider departed to deliver order');

        return redirect()->route('rider.orders')
            ->with('success', '🚚 Order #' . $order->id . ' is now out for delivery!');
    }
}
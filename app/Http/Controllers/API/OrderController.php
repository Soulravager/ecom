<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Http\Requests\OrderRequest;
use Razorpay\Api\Api;
class OrderController extends Controller
{
public function store(Request $request)
{
    try {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $total,
            'status' => 'pending',
            'payment_type' => $request->payment_type,
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        if ($request->payment_type === 'razorpay') {
            $api = new \Razorpay\Api\Api(
                env('RAZORPAY_KEY_ID'),
                env('RAZORPAY_KEY_SECRET')
            );

            $razorpayOrder = $api->order->create([
                'receipt' => 'ORD-' . $order->id,
                'amount' => $total * 100,
                'currency' => 'INR',
            ]);

            $order->update(['payment_id' => $razorpayOrder['id']]);
        }

        return response()->json(['message' => 'Order created', 'order' => $order]);
    } catch (\Exception $e) {
        \Log::error('Order create failed: ' . $e->getMessage());
        \Log::info('RAZORPAY_KEY_ID: ' . env('RAZORPAY_KEY_ID'));
\Log::info('RAZORPAY_KEY_SECRET: ' . env('RAZORPAY_KEY_SECRET'));

        return response()->json(['message' => 'Order creation failed', 'error' => $e->getMessage()], 500);
    }
}


public function index(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $orders = Order::with('items.product')
        ->where('user_id', $user->id)
        ->get();

    return response()->json($orders);
}


    public function show(Request $request,$id)
    {
        $user = $request->user();
        if(!$user) return response()->json(['message'=>'Unauthenticated'],401);
        $order = Order::with('items.product')->where('user_id',$user->id)->find($id);
        return $order ? response()->json($order) : response()->json(['message'=>'Order not found'],404);
    }

    public function updateStatus(Request $request,$id)
    {
        $user = $request->user();
        if(!in_array($user->role->slug,['admin','staff'])) return response()->json(['message'=>'Unauthorized'],403);

        $request->validate(['status'=>'required|string|in:pending,completed,cancelled,refunded']);

        $order = Order::find($id);
        if(!$order) return response()->json(['message'=>'Order not found'],404);

        $order->update(['status'=>$request->status]);
        return response()->json(['message'=>'Order status updated','order'=>$order]);
    }

    public function markPaid(Request $request, $id)
{
    $user = $request->user();
    $order = Order::where('user_id', $user->id)->findOrFail($id);

    $order->update(['status' => 'completed']);

    return response()->json(['message' => 'Payment confirmed', 'order' => $order]);
}
 public function verifyPayment(Request $request)
    {
        $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            $order = Order::where('payment_id', $request->razorpay_order_id)->firstOrFail();

            $order->update(['status' => 'completed']);

            CartItem::where('user_id', $order->user_id)->delete();

            return response()->json(['message' => 'Payment verified successfully', 'order' => $order]);
        } catch (\Exception $e) {            

            if ($request->razorpay_order_id) {
                Order::where('payment_id', $request->razorpay_order_id)->update(['status' => 'failed']);
            }

            return response()->json(['message' => 'Payment verification failed', 'error' => $e->getMessage()], 400);
        }
    }

public function DeliveryStatus(Request $request, $id)
{
    $user = $request->user();

    if (!in_array($user->role->slug, ['admin', 'staff'])) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request->validate([
        'delivery_status' => 'required|string|in:pending,shipped,on_the_way,delivered,cancelled_by_seller',
    ]);

    $order = Order::find($id);
    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    $order->update(['delivery_status' => $request->delivery_status]);

    return response()->json([
        'message' => 'Delivery status updated successfully',
        'order' => $order,
    ]);
}


public function cancelOrder(Request $request, $id)
{
    $user = $request->user();

    $order = Order::where('user_id', $user->id)->find($id);
    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    if (in_array($order->delivery_status, ['delivered', 'cancelled_by_seller', 'cancelled_by_user'])) {
        return response()->json(['message' => 'This order cannot be cancelled'], 400);
    }

    $order->update(['delivery_status' => 'cancelled_by_user']);

    return response()->json([
        'message' => 'Order cancelled successfully',
        'order' => $order,
    ]);
}


public function GetAllOrders(Request $request)
{
    $user = $request->user();

    if (!in_array($user->role->slug, ['admin', 'staff'])) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $orders = \App\Models\Order::with(['user:id,name,email', 'items.product:id,name,price'])
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($orders);
}


}


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
    public function store(OrderRequest $request)
    {
        $user = $request->user();
        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) return response()->json(['message'=>'Cart is empty'],400);

        $total = $cartItems->sum(fn($item)=> $item->product->price * $item->quantity);

        $order = Order::create([
            'user_id'=>$user->id,
            'total_amount'=>$total,
            'status'=>'pending',
            'payment_type'=>$request->payment_type,
            'payment_id'=>Str::upper(Str::random(6))
        ]);

        $cartItems->each(function($item) use ($order){
            OrderItem::create([
                'order_id'=>$order->id,
                'product_id'=>$item->product_id,
                'quantity'=>$item->quantity,
                'price'=>$item->product->price
            ]);
            $item->product->decrement('stock', $item->quantity);
        });

        CartItem::where('user_id',$user->id)->delete();

        return response()->json(['message'=>'Order placed successfully','order'=>$order->load('items.product')],201);
    }

public function index(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    
    $orders = Order::with('items.product')->where('user_id', $user->id)->get();

    
    $orders->each(function ($order) {
        $order->items->each(function ($item) {
            if ($item->product) {
                $item->product->image = $item->product->image
                    ? url('storage/' . $item->product->image)
                    : null;
            }
        });
    });

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

        $request->validate(['status'=>'required|string|in:pending,completed,cancelled']);

        $order = Order::find($id);
        if(!$order) return response()->json(['message'=>'Order not found'],404);

        $order->update(['status'=>$request->status]);
        return response()->json(['message'=>'Order status updated','order'=>$order]);
    }
}

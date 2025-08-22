<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;

class OrderController extends Controller
{
    
    public function store(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|string',
        ]);

        $user = $request->user();
        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();

        if($cartItems->isEmpty()){
            return response()->json(['message'=>'Cart is empty'], 400);
        }

        $totalAmount = 0;
        foreach($cartItems as $item){
            $totalAmount += $item->product->price * $item->quantity;
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_type' => $request->payment_type,
            'payment_id' => Str::upper(Str::random(6)),
        ]);

        foreach($cartItems as $item){
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);

            $product = $item->product;
            $product->stock -= $item->quantity;
            $product->save();
        }

        CartItem::where('user_id', $user->id)->delete();

        return response()->json([
            'message'=>'Order placed successfully',
            'order' => $order->load('items.product')
        ], 201);
    }

    
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = Order::with('items.product')->where('user_id', $user->id)->get();
        return response()->json($orders);
    }

    
    public function show($id, Request $request)
    {
        $user = $request->user();
        $order = Order::with('items.product')->where('user_id', $user->id)->findOrFail($id);
        return response()->json($order);
    }

    
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();

        
        if (!in_array($user->role->slug, ['admin','staff'])) {
            return response()->json(['message'=>'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|string|in:pending,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return response()->json([
            'message'=>'Order status updated',
            'order'=>$order
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CartController extends Controller
{
   public function index()
{
    $cartItems = CartItem::with('product')
        ->where('user_id', Auth::id())
        ->get();

    $totalAmount = $cartItems->sum(function ($item) {
        return $item->product->price * $item->quantity;
    });

    return response()->json([
        'items' => $cartItems,
        'total_amount' => $totalAmount
    ]);
}



    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|uuid|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::create([
            'user_id'    => Auth::id(),
            'product_id' => $request->product_id,
            'quantity'   => $request->quantity,
        ]);

        return response()->json($cartItem, 201);
    }

    public function update(Request $request, $id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json($cartItem);
    }

    public function destroy($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed']);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;

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

    public function store(CartItemRequest $request)
    {
        $validated = $request->validated();

        $cartItem = CartItem::create([
            'user_id'    => Auth::id(),
            'product_id' => $validated['product_id'],
            'quantity'   => $validated['quantity'],
        ]);

        return response()->json($cartItem, 201);
    }

    public function update(UpdateCartItemRequest $request, $id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validated();

        $cartItem->update([
            'quantity' => $validated['quantity'],
        ]);

        return response()->json($cartItem);
    }

    public function destroy($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}

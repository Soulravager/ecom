<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();
        return response()->json($cart);    }

    
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        
        if ($request->quantity > $product->stock) {
            return response()->json(['message' => 'Not enough stock'], 400);
        }

        $cartItem = CartItem::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $request->product_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        return response()->json($cartItem, 201);
    }

    
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);

        if ($request->quantity > $cartItem->product->stock) {
            return response()->json(['message' => 'Not enough stock'], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json($cartItem);
    }

    
    public function destroy($id)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all()->map(function ($product) {
            $product->image = $product->image
                ? url('storage/' . $product->image) 
                : null;
            return $product;
        });

        return response()->json($products);
    }
 
    public function store(ProductRequest $request)
    {
        $productData = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $productData['image'] = $path;
        }

        $product = Product::create($productData);


        $product->image = $product->image
            ? url('storage/' . $product->image)
            : null;

        return response()->json($product, 201);
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $product->image = $product->image
            ? url('storage/' . $product->image)
            : null;

        return response()->json($product);
    }


    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $updateData = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $updateData['image'] = $path;
        }

        $product->update($updateData);

   
        $product->image = $product->image
            ? url('storage/' . $product->image)
            : null;

        return response()->json($product);
    }

    
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }


public function hotProduct()
{
    $hotProducts = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->select(
            'order_items.product_id',
            'products.name as product_name',
            'products.image',
            'products.price',
            DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
            DB::raw('COUNT(order_items.id) as total_orders')
        )
        ->groupBy('order_items.product_id', 'products.name', 'products.image', 'products.price')
        ->orderByDesc('total_quantity_sold')
        ->limit(10)
        ->get()
        ->map(function ($product) {
            $product->image = $product->image
                ? url('storage/' . $product->image)
                : null;
            return $product;
        });

    return response()->json([
        'hot_products' => $hotProducts
    ]);
}

}

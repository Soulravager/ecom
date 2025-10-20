<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\UpdateProductRequest;

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
}

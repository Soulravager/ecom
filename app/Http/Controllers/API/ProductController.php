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
        return response()->json(Product::all());
    }





    public function store(ProductRequest $request)
    {
        $productData = $request->validated();
        $product = Product::create($productData);

        return response()->json($product, 201);
    }







    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }




    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $updateData = $request->validated();

        $product->update($updateData);

        return response()->json($product);
    }




    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Product deleted']);
    }
}

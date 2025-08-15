<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function index()
    {
        return response()->json(Product::all());
    }

    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:100',            
            'stock'       => 'required|integer|min:1',
            'image'       => 'nullable' 
        ]);

        $productData = $request->only(['name', 'description', 'price', 'stock']);      
        

        $product = Product::create($productData);

        return response()->json($product, 201);
    }

    
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $this->validate($request, [
            'name'        => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:100',
            'stock'       => 'sometimes|integer|min:1',
            'image'       => 'nullable'
        ]);

        $updateData = $request->only(['name', 'description', 'price', 'stock']);        
        

        $product->update($updateData);

        return response()->json($product);
    }

   
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Product deleted']);
    }
}

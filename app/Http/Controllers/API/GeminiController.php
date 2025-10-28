<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{

    public function chat(Request $request)
    {
        
        $request->validate([
            'product_id' => 'required|string', 
            'messages' => 'required|array',
        ]);

        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $apiKey = env('GEMINI_API_KEY');
        $model = 'gemini-2.0-flash-exp';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        
        $context = "You are an AI shopping assistant. Use the following product details to answer all questions accurately.
If the user asks something unrelated to this product, politely redirect them back to product information.the basics of the shopping site is as follows its the name is > Shopee, it's an e-commerce platform where users can buy a variety of products online.,
its the site for purchasing computer parts and accessories.if user asks about the specification or specs of product provide them with accurate and concise details search the 
web.tell the price from the price of product

Product details:
Name: {$product->name}
Description: {$product->description} also search online for more details if needed.
Price: ₹{$product->price} only tell this price 
Stock: {$product->stock} dont tell exact stock number but say if it's available or out of stock
" . (!empty($product->specs) ? "Specs: {$product->specs}" : "") . "

Keep answers concise and friendly.";

        
        $contents = [
            ['role' => 'user', 'parts' => [['text' => $context]]], 
        ];

        foreach ($request->messages as $msg) {
            if (!isset($msg['role']) || !isset($msg['text'])) continue;

            $contents[] = [
                'role' => $msg['role'], 
                'parts' => [['text' => $msg['text']]],
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey,
            ])->post($url, [
                'contents' => $contents,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'error' => 'Gemini API request failed.',
                    'details' => $response->json(),
                ], 500);
            }

            $data = $response->json();
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            return response()->json([
                'response' => $text ?? 'No response from Gemini.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    
}

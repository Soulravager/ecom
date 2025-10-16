<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
        ]);

        $apiKey = env('GEMINI_API_KEY');
        $model = 'gemini-2.0-flash-exp'; 

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'X-goog-api-key'=> $apiKey,
            ])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $request->prompt]
                        ]
                    ]
                ]
            ]);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to fetch response from Gemini',
                'details' => $response->json(),
            ], 500);
        }

        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        return response()->json([
            'prompt'   => $request->prompt,
            'response' => $text,
        ]);
    }
}

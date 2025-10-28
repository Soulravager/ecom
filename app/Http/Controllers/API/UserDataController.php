<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserData;
use Illuminate\Support\Facades\Auth;

class UserDataController extends Controller
{
    
    public function getUserData()
    {
        $user = Auth::user();

        $data = UserData::where('user_id', $user->id)->first();

        return response()->json([
            'message' => 'User data retrieved successfully',
            'data' => $data
        ]);
    }

   
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|size:10',
            'country_code' => 'nullable|string|size:2',
            'pincode' => 'nullable|string|size:6',
        ]);

        $userData = UserData::create([
            'user_id' => $user->id,
            'address' => $validated['address'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'country_code' => $validated['country_code'] ?? null,
            'pincode' => $validated['pincode'] ?? null,
        ]);

        return response()->json([
            'message' => 'User data added successfully',
            'data' => $userData
        ], 201);
    }

   
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string|size:10',
            'country_code' => 'nullable|string|size:2',
            'pincode' => 'nullable|string|size:6',
        ]);

        $userData = UserData::where('user_id', $user->id)->first();

        if (!$userData) {
            return response()->json(['message' => 'User data not found'], 404);
        }

        $userData->update($validated);

        return response()->json([
            'message' => 'User data updated successfully',
            'data' => $userData
        ]);
    }
}

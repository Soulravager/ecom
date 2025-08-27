<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

use App\Http\Requests\RegisterUserRequest;


use App\Http\Requests\LoginUserRequest;
class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $customerRole = Role::where('slug', 'customer')->first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $customerRole->id,
        ]);

        $tokenResult = $user->createToken('ecom-token');

        return response()->json([
            'user' => $user->load('role'),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token ? Carbon::parse($tokenResult->token->expires_at)->toDateTimeString() : null,
        ], 201);
    }

    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $tokenResult = $user->createToken('ecom-token');

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $tokenResult->token ? Carbon::parse($tokenResult->token->expires_at)->toDateTimeString() : null,
        ]);
    }

    public function user(\Illuminate\Http\Request $request)
    {
        return response()->json($request->user()->load('role'));
    }

    public function logout(\Illuminate\Http\Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;

class UserManagementController extends Controller
{
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        
        if ($user->role && $user->role->slug === 'admin') {
            return response()->json(['message' => 'Cannot delete another admin'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    
    public function assignStaff($id)
    {
        $user = User::findOrFail($id);
        $staffRole = Role::where('slug', 'staff')->first();

        if (!$staffRole) {
            return response()->json(['message' => 'Staff role not found'], 404);
        }

        $user->role_id = $staffRole->id;
        $user->save();

        return response()->json([
            'message' => 'User assigned as staff',
            'user' => $user->load('role')
        ]);
    }

    
    public function assignUser($id)
    {
        $user = User::findOrFail($id);
        $customerRole = Role::where('slug', 'customer')->first();

        if (!$customerRole) {
            return response()->json(['message' => 'Customer role not found'], 404);
        }

        $user->role_id = $customerRole->id;
        $user->save();

        return response()->json([
            'message' => 'User assigned as customer',
            'user' => $user->load('role')
        ]);
    }

public function assignAdmin(Request $request, $id)
{
    $currentUser = $request->user();

    if (!$currentUser || $currentUser->role->slug !== 'admin') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

  
    $user = User::findOrFail($id);
    $adminRole = Role::where('slug', 'admin')->first();

    if (!$adminRole) {
        return response()->json(['message' => 'Admin role not found'], 404);
    }

    if ($user->id === $currentUser->id) {
        return response()->json(['message' => 'Cant change Current role'], 403);
    }

    $user->role_id = $adminRole->id;
    $user->save();

    return response()->json([
        'message' => 'now admin',
        'user' => $user->load('role')
    ]);
}


    


public function getAllAccounts(Request $request)
{
    $currentUser = $request->user();

    $query = User::with('role')
        ->select('id', 'name', 'email', 'role_id', 'created_at', 'updated_at');

    if ($currentUser) {
        $query->where('id', '!=', $currentUser->id);
    }

    if (!$currentUser || $currentUser->role->slug !== 'admin') {
        $query->whereHas('role', function ($q) {
            $q->where('slug', '!=', 'admin');
        });
    }

    $users = $query->get();

    $formattedUsers = $users->map(function ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_name' => $user->role ? $user->role->name : null,
            'role_slug' => $user->role ? $user->role->slug : null,
        ];
    });

    return response()->json([
        'message' => 'Accounts retrieved successfully',
        'accounts' => $formattedUsers
    ]);
}

}

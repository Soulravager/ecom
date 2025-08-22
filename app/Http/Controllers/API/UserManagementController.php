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
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Cannot delete another admin'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    public function assignStaff($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'staff';
        $user->save();

        return response()->json(['message' => 'User assigned as staff']);
    }
        public function assignUser($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'user';
        $user->save();

        return response()->json(['message' => 'User assigned']);
    }
}

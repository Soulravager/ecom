<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Staff', 'slug' => 'staff'],
            ['name' => 'Customer', 'slug' => 'customer'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                [
                    'id' => (string) Str::uuid(),
                    'name' => $role['name'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();
        $staffRole = DB::table('roles')->where('slug', 'staff')->first();
        $customerRole = DB::table('roles')->where('slug', 'customer')->first();

        
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->id,
        ]);

        User::create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role_id' => $customerRole->id,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AddViewTasksPermissionSeeder extends Seeder
{
    public function run()
    {
        $permission = Permission::create([
            'name' => 'view_tasks',
            'guard_name' => 'web',
            'display_name' => 'View Tasks',
            'description' => '<p>Allows users to view tasks without full management permissions.</p>',
        ]);

        // Assign to specific roles with explicit guard
        $clientRole = Role::where('name', 'client')->where('guard_name', 'web')->first();
        // if ($clientRole) {
        //     $clientRole->givePermissionTo($permission);
        // }
        
        // Or assign to multiple roles
        $roles = Role::whereIn('name', ['client', 'employee'])->where('guard_name', 'web')->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }
    }
}
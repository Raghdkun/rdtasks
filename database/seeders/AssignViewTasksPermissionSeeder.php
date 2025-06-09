<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AssignViewTasksPermissionSeeder extends Seeder
{
    public function run()
    {
        $permission = Permission::where('name', 'view_tasks')
                                ->where('guard_name', 'web')
                                ->first();
        
        if (!$permission) {
            throw new \Exception('view_tasks permission not found');
        }

        // Assign to client role
        $clientRole = Role::where('name', 'client')
                         ->where('guard_name', 'web')
                         ->first();
        
        if ($clientRole) {
            $clientRole->givePermissionTo($permission);
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(
            ['name' => 'role_admin']
        );
        Permission::create(
            ['name' => 'role_permission_user_manager']
        );
        Permission::create(
            ['name' => 'role_service_user_manager']
        );
        Permission::create(
            ['name' => 'role_event_manager']
        );
        Permission::create(
            ['name' => 'role_moderator']
        );
    }
}

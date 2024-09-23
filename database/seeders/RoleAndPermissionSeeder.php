<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $permissions = [
        'role-list',
        'role-create',
        'role-edit',
        'role-delete',
        'language-list',
        'language-create',
        'language-edit',
        'language-delete',
        'translate-list',
        'translate-create',
        'translate-edit',
        'translate-delete',
    ];

    public function run()
    {
        // Create permissions
        foreach ($this->permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Create roles
        $role = Role::firstOrCreate(['name' => 'Admin']);

        $permissionIds = Permission::pluck('id')->toArray();

        $role->permissions()->sync($permissionIds);

        $user = User::where('email','admin@gmail.com')->first();
        if ($user) {
            $user->update(['role_id' => $role->id]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions
        Permission::firstOrCreate(['name' => 'manage tickets']);
        Permission::firstOrCreate(['name' => 'view reports']);
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'view tickets']);

        // Create roles and assign permissions
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions([
            'view tickets',
            'manage tickets',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'manage tickets',
            'view reports',
            'manage users',
            'view tickets',
        ]);

        // Create managers
        $managers = User::factory(3)->create([
            'role' => 'manager',
        ]);

        foreach ($managers as $manager) {
            $manager->assignRole('manager');
        }

        // Create admin
        $admin = User::factory()->admin()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
        ]);

        $admin->assignRole('admin');
    }
}

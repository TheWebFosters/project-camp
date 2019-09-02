<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
                    ['name' => 'employee.create'],
                    ['name' => 'employee.view'],
                    ['name' => 'employee.edit'],
                    ['name' => 'employee.delete'],

                    ['name' => 'employeeNote.create'],
                    ['name' => 'employeeNote.view'],
                    ['name' => 'employeeNote.edit'],
                    ['name' => 'employeeNote.delete'],

                    ['name' => 'customer.create'],
                    ['name' => 'customer.view'],
                    ['name' => 'customer.edit'],
                    ['name' => 'customer.delete'],

                    ['name' => 'contact.create'],
                    ['name' => 'contact.view'],
                    ['name' => 'contact.edit'],
                    ['name' => 'contact.delete'],

                    ['name' => 'customerNote.create'],
                    ['name' => 'customerNote.view'],
                    ['name' => 'customerNote.edit'],
                    ['name' => 'customerNote.delete'],

                    ['name' => 'project.create'],

                    ['name' => 'file.create'],
                    ['name' => 'setting'],
                    ['name' => 'profile.edit'],
                    ['name' => 'sales.invoices'],

                    ['name' => 'knowledge_base.create'],
                    ['name' => 'knowledge_base.view'],
                    ['name' => 'knowledge_base.edit'],
                    ['name' => 'knowledge_base.delete'],

                    ['name' => 'expense.create'],
                    ['name' => 'expense.edit'],
                    ['name' => 'expense.delete'],

                    ['name' => 'leaves.create'],
                    ['name' => 'leaves.edit'],
                    ['name' => 'leaves.delete'],

                    ['name' => 'tickets.create'],
                    ['name' => 'tickets.view'],
                    ['name' => 'tickets.edit'],
                    ['name' => 'tickets.delete'],
                ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        //Superadmin role.
        Role::create(['name' => 'superadmin']);

        //Employee role.
        $employeeRole = Role::create(['name' => 'employee', 'type' => 'employee']);
        $employeeRole->syncPermissions(['profile.edit', 'leaves.create', 'leaves.edit', 'knowledge_base.view']);

        //Customer role.
        $customerRole = Role::create(['name' => 'contact', 'type' => 'contact']);
        $customerRole->syncPermissions(['profile.edit', 'tickets.create', 'tickets.view']);
    }
}

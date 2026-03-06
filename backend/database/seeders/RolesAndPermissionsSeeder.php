<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'create-purchase-request',
            'view-own-purchase-request',
            'view-all-purchase-requests',
            'submit-purchase-request',
            'approve-purchase-request',
            'reject-purchase-request',
            'manage-purchase-orders',
            'manage-invoices',
            'manage-roles',
            'assign-roles',
            'update-permissions'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]); //loops through the permission array and creates them
        }

        $admin = Role::firstOrCreate(['name' => 'admin']); //create the role admin
        $admin->syncPermissions($permissions);  //admin gets all permissions

        $approver = Role::firstOrCreate(['name' => 'approver']); //approver
        $approver->syncPermissions([ //approver permissions
            'approve-purchase-request',
            'reject-purchase-request',
            'view-all-purchase-requests'
        ]);

        $staff = Role::firstOrCreate(['name' => 'staff']); //normal staff
        $staff->syncPermissions([ //staff permissions
            'create-purchase-request',
            'view-own-purchase-request',
            'submit-purchase-request',
        ]);

        //create users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@itechrar.com'],
            ['name' => 'admin', 'password' => Hash::make('password')]
        );

        $adminUser->assignRole('admin');

        $approverUser = User::firstOrCreate(
            ['email' => 'approver@itechrar.com'],
            ['name' => 'approver', 'password' => Hash::make('password')]
        );

        $approverUser->assignRole('approver');

        $staffUser = User::firstOrCreate(
            ['email' => 'staff@itechrar.com'],
            ['name' => 'staff', 'password' => Hash::make('password')]
        );

        $staffUser->assignRole('staff');

    }
}

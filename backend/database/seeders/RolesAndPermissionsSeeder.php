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
            'view-purchase-orders', 
            'view-invoices',  
            'manage-users',
            'manage-roles',
            'view-suppliers',
            'manage-suppliers',
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
            'view-purchase-orders',  // new
            'view-invoices',
        ]);

        //TO DO create suppliers
        

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

        // Create suppliers
        $suppliers = [
            [
                'supplier_name' => 'ABC Office Supplies',
                'contact' => '09171234567',
                'email' => 'abc@officesupplies.com',
            ],
            [
                'supplier_name' => 'XYZ Trading',
                'contact' => '09181234567',
                'email' => 'xyz@trading.com',
            ],
            [
                'supplier_name' => 'Metro Stationery',
                'contact' => '09191234567',
                'email' => 'metro@stationery.com',
            ],
        ];

        $createdSuppliers = [];
        foreach ($suppliers as $supplier) {
            $createdSuppliers[] = \App\Models\Supplier::firstOrCreate(
                ['supplier_name' => $supplier['supplier_name']],
                ['contact' => $supplier['contact'], 'email' => $supplier['email']]
            );
        }

        // Create items linked to suppliers
        $items = [
            ['supplier_id' => $createdSuppliers[0]->id, 'item_name' => 'Ballpen', 'item_price' => 25.00, 'item_description' => 'Black ballpen'],
            ['supplier_id' => $createdSuppliers[0]->id, 'item_name' => 'Notebook', 'item_price' => 85.00, 'item_description' => 'Spiral notebook'],
            ['supplier_id' => $createdSuppliers[0]->id, 'item_name' => 'Printer Paper (Ream)', 'item_price' => 250.00, 'item_description' => 'A4 size, 500 sheets'],
            ['supplier_id' => $createdSuppliers[1]->id, 'item_name' => 'Whiteboard Marker', 'item_price' => 45.00, 'item_description' => 'Black, erasable'],
            ['supplier_id' => $createdSuppliers[1]->id, 'item_name' => 'Stapler', 'item_price' => 150.00, 'item_description' => 'Standard stapler'],
            ['supplier_id' => $createdSuppliers[1]->id, 'item_name' => 'Folder (Pack of 10)', 'item_price' => 120.00, 'item_description' => 'Long size folders'],
            ['supplier_id' => $createdSuppliers[2]->id, 'item_name' => 'Correction Tape', 'item_price' => 35.00, 'item_description' => 'Standard correction tape'],
            ['supplier_id' => $createdSuppliers[2]->id, 'item_name' => 'Sticky Notes (Pack)', 'item_price' => 65.00, 'item_description' => '3x3 inch, 100 sheets'],
        ];

        foreach ($items as $item) {
            \App\Models\Item::firstOrCreate(
                ['item_name' => $item['item_name']],
                [
                    'supplier_id' => $item['supplier_id'],
                    'item_price' => $item['item_price'],
                    'item_description' => $item['item_description'],
                ]
            );
        }

    }
}

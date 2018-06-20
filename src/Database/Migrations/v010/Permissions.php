<?php
namespace UserFrosting\Sprinkle\Dnsadmin\Database\Migrations\v010;

use UserFrosting\Sprinkle\Account\Database\Models\Permission;
use UserFrosting\Sprinkle\Account\Database\Models\Role;
use UserFrosting\System\Bakery\Migration;

class Permissions extends Migration
{
    public $dependencies = [
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\PermissionsTable',
        '\UserFrosting\Sprinkle\Account\Database\Migrations\v400\RolesTable'
    ];

    public function seed()
    {
        // Add default permissions
        $permissions = [
            'uri_dnsadmin' => new Permission([
                'slug' => 'uri_dnsadmin',
                'name' => 'DNS Admin',
                'conditions' => 'always()',
                'description' => 'Gains access to the DNS Admin pages'
            ])
        ];

        foreach ($permissions as $id => $permission) {
            $slug = $permission->slug;
            $conditions = $permission->conditions;
            // Skip if a permission with the same slug and conditions has already been added
            if (!Permission::where('slug', $slug)->where('conditions', $conditions)->first()) {
                $permission->save();
            }
        }

        // Automatically add permissions to particular roles
        $roleAdmin = Role::where('slug', 'site-admin')->first();
        if ($roleAdmin) {
            $roleAdmin->permissions()->syncWithoutDetaching([
                $permissions['uri_dnsadmin']->id,
            ]);
        }
    }
}

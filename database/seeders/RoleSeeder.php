<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            /** PERMISSIONS ADMIN LIEES AUX ORGANIZERS */
            [
                'name' => 'see-organizer-profile',
                'categorie' => 'manage-organizer',
                'guard_name' => 'admin',
            ],


            ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => $perm['guard_name']],
                ['categorie' => $perm['categorie']]
            );
        }
    }
}

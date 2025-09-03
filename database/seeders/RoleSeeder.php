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

            /** PERMISSIONS ADMIN LIEES AUX PERMISSIONS */
            [
                'name' => 'see-prestation',
                'categorie' => 'manage-prestation',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'create-prestation',
                'categorie' => 'manage-prestation',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'edit-prestation',
                'categorie' => 'manage-prestation',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'delete-prestation',
                'categorie' => 'manage-prestation',
                'guard_name' => 'admin',
            ],


            /** PERMISSIONS ADMIN LIEES AUX CONTRATS */
            [
                'name' => 'see-contrat',
                'categorie' => 'manage-contrat',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'create-contrat',
                'categorie' => 'manage-contrat',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'edit-contrat',
                'categorie' => 'manage-contrat',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'delete-contrat',
                'categorie' => 'manage-contrat',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'send-contrat',
                'categorie' => 'manage-contrat',
                'guard_name' => 'admin',
            ],
            [
                'name' => 'download-contrat',
                'categorie' => 'manage-contrat',
                'guard_name' => 'admin',
            ],

            /** PERMISSIONS ADMIN LIEES AU Dashboard */
            [
                'name' => 'see-dashboard',
                'categorie' => 'manage-dashboard',
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

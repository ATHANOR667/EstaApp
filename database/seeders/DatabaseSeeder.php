<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Artiste;
use App\Models\SuperAdmin;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            RoleSeeder::class,
        ]);

        if (SuperAdmin::count() === 0) {
            SuperAdmin::create([
                'email' => null,
                'password' => Hash::make('password'),
            ]);
        }
        if (Admin::count() === 0) {
            Admin::create([
                'nom' => Factory::create()->name,
                'prenom' => Factory::create()->name,
                'matricule' => Str::uuid(),
                'telephone' => Factory::create()->phoneNumber(),
                'email' => 'mdjiepmo@gmail.com',
                'password' => Hash::make('password'),
                'ville' => Factory::create()->city,
                'pays' => Factory::create()->country,

            ]);
        }

        if (Artiste::count() === 0) {
            Artiste::create([
                'nom' => Factory::create()->name,
            ]);
        }
    }
}

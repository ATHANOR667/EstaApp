<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Organizer;
use App\Models\Tenant\Employee;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

// Peut être remplacé ou modifié

// NOUVEAU: Pour gérer RefreshDatabase avec SQLite
// Si vous utilisez RefreshDatabase, assurez-vous de Laravel 10+ et de sa configuration test/database
// Ou utilisez DatabaseMigrations si RefreshDatabase pose problème avec SQLite.

// Option 1 (Préférée avec RefreshDatabase, si le problème persiste, passer à la 2)
// Assurez-vous que votre phpunit.xml est configuré pour SQLite in-memory sans vacuum.
// Et si le problème persiste, il est souvent lié à la façon dont les transactions sont gérées.

// Option 2 (Plus robuste pour SQLite dans les tests) :
// Utiliser DatabaseMigrations
// use Illuminate\Foundation\Testing\RefreshDatabase; // <-- Retirer cette ligne si vous utilisez DatabaseMigrations

class TenantResourceSyncingTest extends TestCase
{
    // Si RefreshDatabase persiste à poser problème avec SQLite, utilisez DatabaseMigrations
    // DatabaseMigrations recrée la base de données pour chaque test, ce qui est plus lent
    // mais évite les problèmes de transaction avec SQLite.
    use DatabaseMigrations; // <-- Utilisez ceci à la place de RefreshDatabase pour SQLite si le problème persiste

    // Si vous tenez absolument à RefreshDatabase et que le problème persiste
    // essayez ceci, mais la migration est souvent plus simple avec SQLite de test
    // protected function getRefreshDatabaseConfig()
    // {
    //     return [
    //         'sqlite' => [
    //             'driver' => 'sqlite',
    //             'database' => ':memory:',
    //             'foreign_key_constraints' => true,
    //             'options' => [
    //                 \PDO::SQLITE_ATTR_FLOG_DISABLE_SYNC => true, // Désactive FSYNC pour la performance
    //                 \PDO::SQLITE_ATTR_FLOG_DISCONNECT => true, // Désactive la déconnexion sur échec FSYNC
    //                 // 'VACUUM' => 'OFF', // Cette option n'existe pas directement, mais on gère autrement
    //             ],
    //         ],
    //     ];
    // }


    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Exécuter les migrations centrales
        $this->artisan('migrate'); // Pas migrate:fresh --seed ici pour les tests RefreshDatabase/DatabaseMigrations
        // car RefreshDatabase/DatabaseMigrations gère ça.

        // Initialiser un tenant temporaire pour que les migrations de tenant puissent s'exécuter
        // et que les seeds de tenant soient appliqués.
        // C'est crucial pour s'assurer que le schéma 'employees' existe dans le tenant
        // et que les rôles Spatie sont présents.
        $tempTenant = Organization::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Temp Test Org',
            'organizer_id' => null, // Peut être null pour un test temporaire
        ]);
        Tenancy::initialize($tempTenant);
        $this->artisan('tenants:migrate');
        $this->artisan('tenants:seed'); // Exécutez votre TenantPermissionsSeeder ici
        Tenancy::end(); // Important de revenir au contexte central

        // Supprimer le tenant temporaire si nécessaire, ou laisser RefreshDatabase/DatabaseMigrations le gérer.
        // $tempTenant->delete();
    }


    /** @test */
    public function an_organizer_is_synced_to_tenant_as_employee_on_creation()
    {
        // 1. Créer un Organizer dans la base de données centrale
        $organizer = Organizer::create([
            'nom' => 'Doe',
            'prenom' => 'Jane',
            'email' => 'jane.doe@test.com',
            'password' => Hash::make('password'),
            'telephone' => '9876543210',
            'pays' => 'US',
            'ville' => 'New York',
            'profile_verification_status' => true,
        ]);

        // 2. Créer une Organization (Tenant) et la lier à l'Organizer
        $organization = Organization::create([
            'id' => (string) \Illuminate\Support\Str::uuid(), // ID du tenant
            'name' => 'Jane\'s Organization',
            'organizer_id' => $organizer->id,
        ]);

        // Attendre un peu pour le job de queue si 'queueable' est true
        // Important: en environnement de test, il est souvent préférable de désactiver la queue
        // ou d'utiliser le facade FakeQueue pour tester le dispatch.
        // Si vous testez avec `php artisan queue:work --stop-when-empty`, ça va fonctionner.
        // Pour les tests unitaires/fonctionnels, on préfère souvent ça:
        // \Illuminate\Support\Facades\Queue::fake(); // au début du test ou du setup
        // ...
        // \Illuminate\Support\Facades\Queue::assertPushed(\Stancl\Tenancy\Jobs\SyncResource::class);

        // Si le queueable est true dans config/tenancy.php et que vous ne fackez pas la queue,
        // vous DEVEZ vous assurer que le worker de queue s'exécute pour que le job soit traité.
        // Dans un test, cela peut être fait en appelant `Artisan::call('queue:work --stop-when-empty')`
        // après la création, ou en utilisant une queue synchrone pour les tests.
        // Pour ce test, nous allons simuler le traitement du job directement si `queueable` est true.
        if (config('tenancy.synced_resources.organizers_as_employees.queueable')) {
            // Traiter tous les jobs en attente immédiatement
            $this->artisan('queue:work', ['--once' => true, '--queue' => 'default']);
        }


        // 3. Initialiser le contexte du tenant et vérifier l'Employee
        Tenancy::initialize($organization);

        // Vérifier la présence de l'Employee dans la base de données du tenant
        $this->assertDatabaseHas('employees', [
            'email' => 'jane.doe@test.com',
            'organizer_global_id' => $organizer->id,
            'nom' => 'Doe',
            'prenom' => 'Jane',
            // N'incluez pas 'password' ici directement car il sera haché
        ], 'tenant'); // Spécifier 'tenant' pour la base de données du tenant

        $employee = Employee::where('organizer_global_id', $organizer->id)->first();
        $this->assertNotNull($employee, "Employee should exist in tenant database.");

        // Optionnel : Vérifier les rôles Spatie si votre seeder du tenant les applique à cet Employee
        // Il faut s'assurer que le rôle 'owner' est bien créé dans le tenant via votre TenantPermissionsSeeder
        if (method_exists($employee, 'hasRole')) {
            $this->assertTrue($employee->hasRole('owner'), "Employee should have 'owner' role.");
        } else {
            $this->fail("Method hasRole does not exist on Employee model. Spatie/Permission might not be fully set up for tenants.");
        }


        Tenancy::end(); // Revenir au contexte central
    }

    // Vous pouvez ajouter d'autres tests ici si besoin
}

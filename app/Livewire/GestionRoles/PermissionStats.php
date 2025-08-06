<?php

namespace App\Livewire\GestionRoles;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionStats extends Component
{
    public Collection $permissions;
    public Collection $roles;

    public array $permissionRoleCounts = [];
    public array $roleUserCounts = [];
    public int $allRolesCount ;

    public string $guardName ;
    public string $userModelClass;
    public string $userIdentifierColumn = 'nom';

    #[on('dataUpdate')]
    public function actualize(): void
    {
        $this->mount();
    }

    public function mount(): void
    {
        $this->allRolesCount = Role::where('guard_name', $this->guardName)->count();

        $authGuards = Config::get('auth.guards');

        if (isset($authGuards[$this->guardName]['provider'])) {
            $providerName = $authGuards[$this->guardName]['provider'];
            $authProviders = Config::get('auth.providers');

            if (isset($authProviders[$providerName]['model'])) {
                $this->userModelClass = $authProviders[$providerName]['model'];
            } else {
                Log::warning("PermissionStats: Modèle utilisateur non trouvé pour le fournisseur '{$providerName}' du guard '{$this->guardName}'. Utilisation de 'App\\Models\\User' par défaut.");
            }
        } else {
            Log::warning("PermissionStats: Guard '{$this->guardName}' ou son fournisseur non trouvé. Utilisation de 'App\\Models\\User' par défaut.");
        }

        $this->loadStats();
    }

    public function loadStats(): void
    {
        $this->permissions = Permission::where('guard_name', $this->guardName)->orderBy('categorie')->orderBy('name')->get();
        $this->roles = Role::where('guard_name', $this->guardName)->orderBy('name')->get();

        $this->calculatePermissionRoleCounts();
        $this->calculateRoleUserCounts();

    }

    protected function calculatePermissionRoleCounts(): void
    {
        $stats = [];
        foreach ($this->permissions as $permission) {
            $rolesWithPermission = $permission->roles()->where('guard_name', $this->guardName)->get();
            $stats[$permission->id] = [
                'name' => $permission->name,
                'categorie' => $permission->categorie,
                'count' => $rolesWithPermission->count(),
                'roles' => $rolesWithPermission->pluck('name')->toArray(),
            ];
        }

        uasort($stats, function ($a, $b) {
            $catCompare = strcmp($a['categorie'], $b['categorie']);
            if ($catCompare === 0) {
                return strcmp($a['name'], $b['name']);
            }
            return $catCompare;
        });
        $this->permissionRoleCounts = $stats;
    }

    protected function calculateRoleUserCounts(): void
    {
        $stats = [];

        foreach ($this->roles as $role) {

            $usersWithRole = $this->userModelClass::role($role->name, $this->guardName)->get();
            $stats[$role->id] = [
                'name' => $role->name,
                'count' => $usersWithRole->count(),
                'users' => $usersWithRole->pluck($this->userIdentifierColumn)->toArray(),
            ];
        }
        $this->roleUserCounts = $stats;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.gestion-roles.permission-stats');
    }
}

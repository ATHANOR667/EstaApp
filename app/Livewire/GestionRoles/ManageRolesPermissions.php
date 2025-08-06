<?php

namespace App\Livewire\GestionRoles;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ManageRolesPermissions extends Component
{
    public string $newRoleName = '';
    public ?int $selectedRoleId = null;
    public ?Role $selectedRole = null;
    public array $rolePermissions = [];

    public Collection $roles;
    public Collection $permissions;

    public string $guardName ;

    public function getAllPermissionsGroupedProperty(): Collection
    {
        return $this->permissions->groupBy('categorie');
    }

    protected function rules(): array
    {
        return [
            'newRoleName' => [
                'required',
                'string',
                'min:3',
                Rule::unique('roles', 'name')->where(fn ($query) => $query->where('guard_name', $this->guardName)),
            ],
            'selectedRoleId' => 'nullable|exists:roles,id',
            'rolePermissions' => 'nullable|array',
            'rolePermissions.*' => 'exists:permissions,id',
        ];
    }

    protected array $messages = [
        'newRoleName.required' => 'Le nom du rôle est obligatoire.',
        'newRoleName.min' => 'Le nom du rôle doit contenir au moins :min caractères.',
        'newRoleName.unique' => 'Ce nom de rôle existe déjà pour ce guard.', // Mettre à jour le message
        'rolePermissions.*.exists' => 'Une permission sélectionnée n\'est pas valide.'
    ];

    public function mount(): void
    {
        $this->loadRolesAndPermissions();
    }

    public function loadRolesAndPermissions(): void
    {
        $this->roles = Role::where('guard_name', $this->guardName)->orderBy('name')->get();
        $this->permissions = Permission::where('guard_name', $this->guardName)->orderBy('categorie')->orderBy('name')->get();

        if ($this->selectedRoleId) {
            $this->selectedRole = Role::findById($this->selectedRoleId, $this->guardName);
            if ($this->selectedRole) {
                $this->rolePermissions = $this->selectedRole->permissions->pluck('id')->toArray();
            } else {
                $this->selectedRoleId = null;
                $this->rolePermissions = [];
                session()->flash('error', "Le rôle précédemment sélectionné n'est plus disponible pour le guard '{$this->guardName}'.");
            }
        } else {
            $this->selectedRole = null;
            $this->rolePermissions = [];
        }
    }

    public function createRole(): void
    {
        $this->validateOnly('newRoleName');

        try {
            Role::create(['name' => $this->newRoleName, 'guard_name' => $this->guardName]);
            session()->flash('success', 'Rôle "' . $this->newRoleName . '" créé avec succès pour le guard "' . $this->guardName . '".');
            $this->reset('newRoleName');
            $this->loadRolesAndPermissions();
            $this->dispatch('dataUpdate');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création du rôle: ' . $e->getMessage());
            Log::error('ManageRolesPermissions - Erreur createRole: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    public function selectRole(int $roleId): void
    {
        $this->selectedRoleId = $roleId;
        $this->selectedRole = Role::findById($roleId, $this->guardName);

        if ($this->selectedRole) {
            $this->rolePermissions = $this->selectedRole->permissions->pluck('id')->toArray();
        } else {
            $this->selectedRoleId = null;
            $this->rolePermissions = [];
            session()->flash('error', "Le rôle avec l'ID {$roleId} n'a pas été trouvé ou n'appartient pas au guard '{$this->guardName}'.");
        }
    }

    public function updateRolePermissions(): void
    {
        $this->validate([
            'rolePermissions' => 'nullable|array',
            'rolePermissions.*' => 'exists:permissions,id',
        ]);

        if (!$this->selectedRole) {
            session()->flash('error', 'Aucun rôle sélectionné pour mettre à jour les permissions.');
            return;
        }

        try {
            $permissionsToSync = Permission::whereIn('id', $this->rolePermissions)
                ->where('guard_name', $this->guardName)
                ->get();

            $this->selectedRole->syncPermissions($permissionsToSync);

            session()->flash('success', 'Permissions mises à jour pour le rôle "' . $this->selectedRole->name . '" du guard "' . $this->guardName . '".');
            $this->selectRole($this->selectedRoleId);
            $this->dispatch('dataUpdate');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour des permissions: ' . $e->getMessage());
            Log::error('ManageRolesPermissions - Erreur updateRolePermissions: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    public function deleteRole(int $roleId): void
    {
        try {
            $roleToDelete = Role::findById($roleId, $this->guardName);

            if ($roleToDelete) {

                $roleName = $roleToDelete->name;
                $roleToDelete->delete();
                session()->flash('success', 'Rôle "' . $roleName . '" supprimé avec succès pour le guard "' . $this->guardName . '".');
                if ($this->selectedRoleId === $roleId) {
                    $this->reset(['selectedRoleId', 'selectedRole', 'rolePermissions']);
                }
            } else {
                session()->flash('error', "Le rôle avec l'ID {$roleId} n'a pas été trouvé ou n'appartient pas au guard '{$this->guardName}'.");
            }
            $this->dispatch('dataUpdate');
            $this->loadRolesAndPermissions();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression du rôle: ' . $e->getMessage());
            Log::error('ManageRolesPermissions - Erreur deleteRole: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.gestion-roles.manage-roles-permissions');
    }
}

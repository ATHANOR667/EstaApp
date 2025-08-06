@extends('super-admin.connected-base')

@section('title','MANAGE ADMINS')

@section('content')
    <div class="space-y-8">
        {{-- Section pour la gestion des rôles et permissions --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Gestion des Rôles et Permissions</h2>
            @livewire('gestion-roles.manage-roles-permissions', ['guardName' => 'admin'])
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                @livewire('gestion-roles.permission-stats', ['guardName' => 'admin'])
            </div>
        </div>

        {{-- Section pour la gestion des administrateurs --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Gestion des Administrateurs</h2>
            @livewire('super-admin.manage-admins.list-admins')
        </div>
    </div>

    {{-- Modales qui doivent être rendues en dehors du flux principal pour être en surcouche --}}
    @livewire('super-admin.manage-admins.create-admin')
    @livewire('super-admin.manage-admins.admin-profile-card')

@endsection

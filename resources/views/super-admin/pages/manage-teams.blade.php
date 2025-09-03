@extends('super-admin.connected-base')

@section('title', 'MANAGE TEAMS')

@section('content')
    <div
        x-data="{
            showSpinner: false,
            init() {
                Livewire.on('loading', () => {
                    this.showSpinner = true;
                });
                Livewire.on('success', () => {
                    this.showSpinner = false;
                });
                Livewire.on('error', () => {
                    this.showSpinner = false;
                });
            }
        }"
        class="min-h-screen flex flex-col gap-6 p-4 sm:p-6"
    >
        <div x-show="showSpinner"
             class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-50">
            <div class="animate-spin rounded-full h-24 w-24 sm:h-32 sm:w-32 border-t-4 border-b-4 border-blue-500"></div>
        </div>

        <!-- Composant de la liste des artistes et du formulaire de création/édition -->
        <div class="w-full">
            @livewire('super-admin.manage-teams.artistes-list')
        </div>

        <!-- Composant de gestion de l'équipe des admins -->
        <div class="w-full">
            @livewire('super-admin.manage-teams.team-manager')
        </div>

        <!-- Modale de création/édition d'artiste -->
        @livewire('super-admin.manage-teams.artiste-form')

        <!-- Composant de la carte de profil de l'administrateur -->
        @livewire('super-admin.manage-admins.admin-profile-card')
    </div>
@endsection

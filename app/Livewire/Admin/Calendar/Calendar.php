<?php

namespace App\Livewire\Admin\Calendar;

use App\Models\Prestation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class Calendar extends Component
{
    public  Carbon $currentDate;
    public string $viewMode = 'month';

    public Collection $prestations;

    #[On('refreshCalendar')]
    public function refresh(): void
    {
        $this->prestationsForCurrentPeriod();
        switch ($this->viewMode) {
            case 'month':
                $this->dispatch('refreshMonthView',
                    prestationIds : $this->prestations->pluck('id')->toArray());
                break;
            case 'day':
                $this->dispatch('refreshDayView',
                    prestationIds : $this->prestations->pluck('id')->toArray());
                break;
        }
    }
    public function mount(): void
    {
        Gate::authorize('see-prestation');

        $this->currentDate = Carbon::now();
    }

    /**
     * Propriété calculée pour récupérer les prestations de la période actuellement affichée.
     * Inclut la relation 'artiste' pour l'affichage des informations de l'artiste.
     *
     * @return Collection
     */
    public function prestationsForCurrentPeriod() :void
    {
        $query = Prestation::query();

        // Détermine la période de début et de fin en fonction du mode de vue
        switch ($this->viewMode) {
            case 'month':
                $start = $this->currentDate->copy()->startOfMonth()->startOfDay();
                $end = $this->currentDate->copy()->endOfMonth()->endOfDay();
                break;
            case 'day':
                $start = $this->currentDate->copy()->startOfDay();
                $end = $this->currentDate->copy()->endOfDay();
                break;
            default:
                $this->prestations =  collect();
        }

        $admin = Auth::guard('admin')->user();
        $this->prestations = $query->whereBetween('date_prestation', [$start, $end])
            ->with('artiste')
            ->whereHas('artiste', function ($query) use ($admin) {
                $query->whereIn('id', $admin->artistes()->pluck('artistes.id'));
            })
            ->orderBy('date_prestation')
            ->orderBy('heure_debut_prestation')
            ->get();
    }


    public function goToPrevious(): void
    {
        switch ($this->viewMode) {
            case 'month':
                $this->currentDate->subMonth();
                break;
            case 'day':
                $this->currentDate->subDay();
                break;
        }
        // Crée une nouvelle instance de Carbon pour forcer Livewire à détecter le changement
        $this->currentDate = $this->currentDate->copy();
    }

    /**
     * Passe à la période suivante (mois ou jour) en fonction du mode de vue.
     */
    public function goToNext(): void
    {
        switch ($this->viewMode) {
            case 'month':
                $this->currentDate->addMonth();
                break;
            case 'day':
                $this->currentDate->addDay();
                break;
        }
        // Crée une nouvelle instance de Carbon pour forcer Livewire à détecter le changement
        $this->currentDate = $this->currentDate->copy();
    }

    /**
     * Passe à l'année précédente (uniquement en mode mois).
     */
    public function goToPreviousYear(): void
    {
        if ($this->viewMode === 'month') {
            $this->currentDate->subYear();
            $this->currentDate = $this->currentDate->copy();
        }
    }

    /**
     * Passe à l'année suivante (uniquement en mode mois).
     */
    public function goToNextYear(): void
    {
        if ($this->viewMode === 'month') {
            $this->currentDate->addYear();
            $this->currentDate = $this->currentDate->copy();
        }
    }

    /**
     * Définit le mode de vue du calendrier ('month' ou 'day').
     * Peut également définir une date spécifique si le mode est 'day'.
     *
     * @param string $mode Le mode de vue souhaité.
     * @param string|null $dateOptionnelle Date spécifique si passage en mode 'day' (format Y-m-d).
     */

    #[On('set-view-mode')]
    public function setViewMode(string $mode, string $dateOptionnelle = null): void
    {
        $this->viewMode = $mode;
        if ($mode === 'day' && $dateOptionnelle) {
            $this->currentDate = Carbon::parse($dateOptionnelle);
        } else {
            // Si on change de mode sans date spécifique, ou si on repasse en mois,
            // on force le re-rendu avec la date actuelle du composant.
            $this->currentDate = $this->currentDate->copy();
        }
    }

    /**
     * Propriété calculée pour obtenir le titre de la période actuelle en français.
     *
     * @return string
     */
    public function getCurrentPeriodTitleProperty(): string
    {
        switch ($this->viewMode) {
            case 'month':
                return $this->currentDate->locale('fr')->monthName . ' ' . $this->currentDate->year;
            case 'day':
                return $this->currentDate->locale('fr')->isoFormat('dddd D MMMM YYYY');
            default:
                return '';
        }
    }

    public function openPrestationFormModal(): void
    {
        $this->dispatch('open-prestation-form');
    }


    public function render(): \Illuminate\Contracts\View\View
    {
        $this->prestationsForCurrentPeriod();
        return view('livewire.admin.calendar.calendar');
    }
}

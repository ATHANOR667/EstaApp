<?php

namespace App\Livewire\Admin\Calendar\ViewMode;

use App\Models\Prestation;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class DayView extends Component
{
    public Carbon $currentDate;
    public Collection $prestations;


    #[On('refreshDayView')]
    public function refresh( array $prestationIds): void
    {
        $prestations = Prestation::with('artiste')
            ->whereIn('id', $prestationIds)
            ->get();
        $this->mount($this->currentDate, $prestations);
        $this->render();
    }

    public function mount(Carbon $currentDate, Collection $prestations): void
    {
        $this->currentDate = $currentDate;

        $this->prestations = $prestations->filter(function ($prestation) {
            return Carbon::parse($prestation->date_prestation)->isSameDay($this->currentDate);
        })->sortBy(function ($prestation) {
            return Carbon::parse($prestation->heure_debut_prestation);
        });
    }


    public function updated(string $propertyName): void
    {
        if ($propertyName === 'currentDate' || $propertyName === 'prestations') {
            $this->prestations = $this->prestations->filter(function ($prestation) {
                return Carbon::parse($prestation->date_prestation)->isSameDay($this->currentDate);
            })->sortBy(function ($prestation) {
                return Carbon::parse($prestation->heure_debut_prestation);
            });
        }
    }


    public function openPrestationDetails(int $prestationId): void
    {
        $this->dispatch('open-prestation-details',
            date: $this->currentDate->format('Y-m-d'),
            prestationId: $prestationId
        );
    }


    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.calendar.view-mode.day-view');
    }
}

<?php

namespace App\Livewire\Admin\Calendar;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Prestation;
use Illuminate\Support\Collection;

class DayView extends Component
{
    public Carbon $currentDate;
    public \Illuminate\Database\Eloquent\Collection $prestations;


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
        $this->dispatch('open-prestation-details', date: $this->currentDate->format('Y-m-d'), prestationId: $prestationId);
    }


    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.calendar.day-view');
    }
}

<?php

namespace App\Livewire\Admin\Calendar;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Carbon\Carbon;

class MonthView extends Component
{
    public Carbon $currentDate;
    public Collection $prestations;

    public int $daysInMonth;
    public Carbon $firstDayOfMonth;
    public int $blankDaysBefore;

    public function mount(Carbon $currentDate, Collection $prestations): void
    {
        $this->currentDate = $currentDate;
        $this->prestations = $prestations;
        $this->calculateCalendarDays();
    }


    public function updated(string $propertyName): void
    {
        if ($propertyName === 'currentDate' || $propertyName === 'prestations') {
            $this->calculateCalendarDays();
        }
    }


    private function calculateCalendarDays(): void
    {
        $this->daysInMonth = $this->currentDate->daysInMonth;
        $this->firstDayOfMonth = $this->currentDate->copy()->startOfMonth();

        // Carbon::dayOfWeekIso renvoie 1 pour Lundi, 7 pour Dimanche.
        // On veut que Lundi soit le premier jour de la semaine (index 0 pour les jours vides).
        $this->blankDaysBefore = $this->firstDayOfMonth->dayOfWeekIso - 1;
        if ($this->blankDaysBefore < 0) { // Ajustement pour le cas où la semaine commence un dimanche (dayOfWeekIso = 7)
            $this->blankDaysBefore = 6;
        }
    }


    public function getPrestationsForDay( string $date):Collection
    {
        return $this->prestations->filter(function ($prestation) use ($date) {
            return Carbon::parse($prestation->date_prestation)->format('Y-m-d') === $date;
        });
    }


    public function openDayDetails($date): void
    {
        $this->dispatch('set-view-mode', mode: 'day', dateOptionnelle: $date);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.calendar.month-view');
    }
}

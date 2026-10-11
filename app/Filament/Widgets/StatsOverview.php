<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pacientes Totales', Patient::count())
                ->icon('heroicon-o-users'),

            Stat::make('Citas Pendientes', Appointment::where('status', 'pendiente')->count())
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Citas Hoy', Appointment::whereDate('date', Carbon::today())->count())
                ->color('success')
                ->icon('heroicon-o-calendar'),
        ];
    }
}

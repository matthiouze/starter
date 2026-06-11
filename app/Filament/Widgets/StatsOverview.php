<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make(__('dashboard.stats.users'), User::count())
                ->description(__('dashboard.stats.users_description'))
                ->color('primary'),
            Stat::make(__('dashboard.stats.roles'), Role::count())
                ->description(__('dashboard.stats.roles_description')),
            Stat::make(
                __('dashboard.stats.activities'),
                Activity::where('created_at', '>=', now()->subDays(30))->count(),
            )
                ->description(__('dashboard.stats.activities_description')),
        ];
    }
}

<?php

use App\Filament\Widgets\LatestActivities;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\UsersChart;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('renders the dashboard widgets', function (string $widget) {
    actingAs(User::factory()->create());

    Livewire::test($widget)->assertOk();
})->with([
    StatsOverview::class,
    UsersChart::class,
    LatestActivities::class,
]);

it('counts users in the stats overview', function () {
    User::factory()->count(3)->create();

    actingAs(User::factory()->create());

    Livewire::test(StatsOverview::class)->assertSee('4');
});
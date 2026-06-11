<?php

use App\Filament\Pages\ManageGeneralSettings;
use App\Models\User;
use App\Settings\GeneralSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

function superAdmin(): User
{
    $role = Role::firstOrCreate(['name' => Utils::getSuperAdminName(), 'guard_name' => 'web']);

    return User::factory()->create()->assignRole($role);
}

it('forbids access to non super admins', function () {
    actingAs(User::factory()->create());

    expect(ManageGeneralSettings::canAccess())->toBeFalse();
});

it('allows super admins to access the page', function () {
    actingAs(superAdmin());

    expect(ManageGeneralSettings::canAccess())->toBeTrue();

    Livewire\Livewire::test(ManageGeneralSettings::class)->assertOk();
});

it('persists the general settings', function () {
    actingAs(superAdmin());

    Livewire\Livewire::test(ManageGeneralSettings::class)
        ->fillForm([
            'site_name' => 'Mon Starter',
            'support_email' => 'support@example.com',
            'maintenance_mode' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(GeneralSettings::class);

    expect($settings->site_name)->toBe('Mon Starter')
        ->and($settings->support_email)->toBe('support@example.com')
        ->and($settings->maintenance_mode)->toBeTrue();
});
<?php

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('renders the profile page with information and security tabs', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(EditProfile::class)
        ->assertOk()
        ->assertSee(__('users.profile_tab'))
        ->assertSee(__('users.security_tab'));
});

it('updates profile information from the form', function () {
    $user = User::factory()->create();

    actingAs($user);

    Livewire::test(EditProfile::class)
        ->fillForm([
            'firstname' => 'Jean',
            'lastname' => 'Dupont',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->refresh())
        ->firstname->toBe('Jean')
        ->lastname->toBe('Dupont');
});
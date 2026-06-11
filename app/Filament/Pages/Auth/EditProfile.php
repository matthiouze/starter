<?php

namespace App\Filament\Pages\Auth;

use BackedEnum;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;

class EditProfile extends BaseEditProfile
{
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedUserCircle;
    }

    public static function getNavigationLabel(): string
    {
        return __('users.profile');
    }

    public function getTitle(): string
    {
        return __('users.profile');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make(__('users.profile_tab'))
                            ->icon(Heroicon::OutlinedUserCircle)
                            ->schema([
                                $this->getFormContentComponent(),
                            ]),
                        Tab::make(__('users.security_tab'))
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->schema(Arr::wrap($this->getMultiFactorAuthenticationContentComponent())),
                    ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getLastnameFormComponent(),
                $this->getFirstnameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                $this->getCurrentPasswordFormComponent(),
            ]);
    }

    protected function getLastnameFormComponent(): Component
    {
        return TextInput::make('lastname')
            ->label(__('users.lastname'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getFirstnameFormComponent(): Component
    {
        return TextInput::make('firstname')
            ->label(__('users.firstname'))
            ->required()
            ->maxLength(255);
    }
}

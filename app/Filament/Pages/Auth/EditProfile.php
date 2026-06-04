<?php

namespace App\Filament\Pages\Auth;

use BackedEnum;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

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

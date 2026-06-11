<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageGeneralSettings extends SettingsPage
{
    protected static string $settings = GeneralSettings::class;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole(Utils::getSuperAdminName()) ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('settings.navigation_group');
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedCog6Tooth;
    }

    public static function getNavigationLabel(): string
    {
        return __('settings.title');
    }

    public function getTitle(): string
    {
        return __('settings.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('site_name')
                    ->label(__('settings.site_name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('support_email')
                    ->label(__('settings.support_email'))
                    ->email()
                    ->maxLength(255),
                Toggle::make('maintenance_mode')
                    ->label(__('settings.maintenance_mode'))
                    ->helperText(__('settings.maintenance_mode_helper')),
            ]);
    }
}

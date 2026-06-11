<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('users.information_tab'))
                            ->icon(Heroicon::OutlinedUser)
                            ->columns(2)
                            ->schema([
                                TextInput::make('lastname')
                                    ->label(__('users.lastname'))
                                    ->required(),
                                TextInput::make('firstname')
                                    ->label(__('users.firstname'))
                                    ->required(),
                                TextInput::make('email')
                                    ->label(__('users.email'))
                                    ->email()
                                    ->required(),
                                DateTimePicker::make('email_verified_at')
                                    ->label(__('users.email_verified_at')),
                                TextInput::make('password')
                                    ->label(__('users.password'))
                                    ->password()
                                    ->revealable()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->dehydrated(fn (?string $state): bool => filled($state)),
                            ]),
                        Tab::make(__('users.roles_permissions_tab'))
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->schema([
                                Select::make('roles')
                                    ->label(__('users.roles'))
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                                Select::make('permissions')
                                    ->label(__('users.permissions'))
                                    ->relationship('permissions', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable(),
                            ]),
                    ]),
            ]);
    }
}

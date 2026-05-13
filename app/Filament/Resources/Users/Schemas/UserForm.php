<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->required(),
            ]);
    }
}

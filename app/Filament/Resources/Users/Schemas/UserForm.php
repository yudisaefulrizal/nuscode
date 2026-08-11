<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->helperText('Kosongkan kalau tidak ingin mengubah password saat edit.'),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'client' => 'Client',
                    ])
                    ->default('admin')
                    ->required()
                    ->helperText('Resource ini untuk kelola akun admin. Akun client dibuat otomatis saat checkout.'),
            ]);
    }
}

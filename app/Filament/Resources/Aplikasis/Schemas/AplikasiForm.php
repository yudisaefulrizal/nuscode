<?php

namespace App\Filament\Resources\Aplikasis\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AplikasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('demo_url')
                    ->url(),
                FileUpload::make('image')
                    ->image()
                    ->disk('public'),
                TextInput::make('tech_stack')
                    ->helperText('Pisahkan dengan koma, mis. Laravel, React, MySQL'),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}

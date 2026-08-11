<?php

namespace App\Filament\Resources\Prompts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PromptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                FileUpload::make('file_path')
                    ->label('File ZIP')
                    ->disk('local')
                    ->acceptedFileTypes(['application/zip', 'application/x-zip-compressed'])
                    ->maxSize(102400)
                    ->required(),
                Textarea::make('prompt_content')
                    ->columnSpanFull(),
                Textarea::make('example_output')
                    ->columnSpanFull(),
                TextInput::make('category'),
                FileUpload::make('image')
                    ->image(),
            ]);
    }
}

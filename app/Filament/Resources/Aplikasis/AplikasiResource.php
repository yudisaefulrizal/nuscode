<?php

namespace App\Filament\Resources\Aplikasis;

use App\Filament\Resources\Aplikasis\Pages\CreateAplikasi;
use App\Filament\Resources\Aplikasis\Pages\EditAplikasi;
use App\Filament\Resources\Aplikasis\Pages\ListAplikasis;
use App\Filament\Resources\Aplikasis\Schemas\AplikasiForm;
use App\Filament\Resources\Aplikasis\Tables\AplikasisTable;
use App\Models\Aplikasi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AplikasiResource extends Resource
{
    protected static ?string $model = Aplikasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AplikasiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AplikasisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAplikasis::route('/'),
            'create' => CreateAplikasi::route('/create'),
            'edit' => EditAplikasi::route('/{record}/edit'),
        ];
    }
}

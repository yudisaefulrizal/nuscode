<?php

namespace App\Filament\Resources\Aplikasis\Pages;

use App\Filament\Resources\Aplikasis\AplikasiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAplikasis extends ListRecords
{
    protected static string $resource = AplikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

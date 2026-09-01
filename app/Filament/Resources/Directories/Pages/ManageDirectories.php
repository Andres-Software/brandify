<?php

namespace App\Filament\Resources\Directories\Pages;

use App\Filament\Resources\Directories\DirectoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDirectories extends ManageRecords
{
    protected static string $resource = DirectoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

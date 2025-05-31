<?php

namespace Avexsoft\FilamentPurl\Filament\Resources\RedirectResource\Pages;

use Avexsoft\FilamentPurl\Filament\Resources\RedirectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRedirect extends EditRecord
{
    protected static string $resource = RedirectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

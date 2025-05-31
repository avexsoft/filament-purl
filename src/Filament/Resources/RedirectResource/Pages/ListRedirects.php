<?php

namespace Avexsoft\FilamentPurl\Filament\Resources\RedirectResource\Pages;

use Avexsoft\FilamentPurl\Filament\Resources\RedirectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ListRedirects extends ListRecords
{
    protected static string $resource = RedirectResource::class;

    protected ?string $subheading = '';

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString('Maintain permalinks with HTTP redirects + other Filament plugins <u><a href="https://github.com/mxts">here</a></u>');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace Avexsoft\FilamentPurl\Filament;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

abstract class FilamentPurlResource extends Resource
{
    public static function registerNavigationItems(): void
    {
        static::$navigationGroup = 'Purl';
        parent::registerNavigationItems();
    }

    public static function tableDefaults($table)
    {
        $pages = static::getPages();
        $viewPageExists = isset($pages['view']);

        if ($viewPageExists) {
            // when row is clicked, go to view (instead of edit)
            $table->recordUrl(function (Model $record) use ($pages): string {
                return $pages['view']->getPage()::getUrl([$record->id]);
            });
        }

        // sort by id desc
        return $table
            ->searchable()
            ->defaultSort('id', 'desc');
    }
}

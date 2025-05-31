<?php

namespace Avexsoft\FilamentPurl\Filament\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;

class HumanDateColumn extends TextColumn
{
    public static function make($name): static
    {
        $key = $name;

        return parent::make($name)
            ->getStateUsing(function ($record) use ($key) {
                if ($record->$key) {
                    return Carbon::parse($record->$key)->diffForHumans();
                }

                return '&dash;';
            })
            ->html()
            ->tooltip(function ($record) use ($key) {
                return $record->$key;
            })
            ->extraHeaderAttributes(['class' => 'w-1']);

    }
}

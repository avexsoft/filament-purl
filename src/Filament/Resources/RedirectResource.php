<?php

namespace Avexsoft\FilamentPurl\Filament\Resources;

use Avexsoft\FilamentPurl\Filament\Resources\RedirectResource\Pages;
use Avexsoft\FilamentPurl\Filament\Tables\Columns\HumanDateColumn;
use Avexsoft\FilamentPurl\Models\Redirect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RedirectResource extends \Avexsoft\FilamentPurl\Filament\FilamentPurlResource
{
    protected static ?string $model = Redirect::class;

    protected static ?string $navigationIcon = 'heroicon-m-link';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('slug')
                    // ->live(debounce: 500)
                    // ->afterStateUpdated(fn (Set $set, ?string $state) => $set('path', Str::slug($state)))
                    // ->label('Slug')
                    ->required(),
                TextInput::make('url')
                    // ->required()
                    // ->url()
                    ->label('Destination'),
                Textarea::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return static::tableDefaults($table)
            ->columns([
                ToggleColumn::make('is_active')
                    ->extraHeaderAttributes(['class' => 'w-1']),
                TextColumn::make('id')
                    ->extraHeaderAttributes(['class' => 'w-1'])
                    ->sortable(),
                TextColumn::make('slug')
                    ->extraHeaderAttributes(['class' => 'w-1'])
                    ->url(fn ($record) => route('purl', $record->slug), true),
                TextColumn::make('url')
                    ->label('Destination')
                    ->formatStateUsing(fn ($record): string => Str::of($record->url)->limit(20))
                    ->tooltip(function ($record): ?string {
                        return $record->url;
                    })
                    ->extraHeaderAttributes(['class' => 'w-1'])
                    ->sortable(),
                TextColumn::make('description'),
                HumanDateColumn::make('created_at')
                    ->sortable(),
                HumanDateColumn::make('updated_at')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index'  => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit'   => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}

<?php

namespace Avexsoft\FilamentPurl;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentPurlPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-purl';
    }

    public function register(Panel $panel): void
    {
        $resources = [
            'Avexsoft\\FilamentPurl\\Filament\\Resources' => realpath(base_path('vendor/avexsoft/filament-purl/src/Filament/Resources')),
        ];

        foreach ($resources as $namespace => $path) {
            $panel->discoverResources(
                for: $namespace,
                in: $path
            );
        }

    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}

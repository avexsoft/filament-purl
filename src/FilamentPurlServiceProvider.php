<?php

namespace Avexsoft\FilamentPurl;

use Illuminate\Support\ServiceProvider;

class FilamentPurlServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'avexsoft');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'avexsoft');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $paths = [__DIR__.'/../database/migrations'];
        $this->callAfterResolving('migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filament-purl.php', 'filament-purl');

        // Register the service the package provides.
        $this->app->singleton('filament-purl', function ($app) {
            return new FilamentPurl;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['filament-purl'];
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/filament-purl.php' => config_path('filament-purl.php'),
        ], 'filament-purl.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/avexsoft'),
        ], 'filament-purl.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/avexsoft'),
        ], 'filament-purl.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/avexsoft'),
        ], 'filament-purl.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}

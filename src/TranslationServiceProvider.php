<?php

namespace Snayvik\Translation;
use Illuminate\Support\ServiceProvider;
use Snayvik\Translation\Console\Commands\ImportTranslationInDatabaseCommand;
use Snayvik\Translation\console\Commands\ImportTranslationInFilesCommand;

class TranslationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->publishes([
            __DIR__.'/Config/translation.php' => config_path('SnayvikTranslation.php')
        ], 'snayvik-translation-config');

        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'SnayvikTranslationView');

        $this->publishes([
            __DIR__.'/Database/migrations/' => database_path('migrations')
        ], 'snayvik-translation-migrations');

        // if ($this->app->runningInConsole()) {
            $this->commands([
                ImportTranslationInFilesCommand::class,
                ImportTranslationInDatabaseCommand::class
            ]);
        // }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/translation.php', 'SnayvikTranslation'
        );
    }
}
?>
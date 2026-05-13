<?php

namespace Light2000\Modeler;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Light2000\Modeler\Console\GenerateCommand;
use Light2000\Modeler\Console\InstallCommand;
use Light2000\Modeler\Console\StudioCommand;
use Light2000\Modeler\Support\BinaryDownloader;

class ModelerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/modeler.php', 'modeler');

        $packageDefaults = require __DIR__ . '/../config/modeler.php';
        $config = $this->app['config']->get('modeler', []);
        $config['setting'] = array_merge(
            $packageDefaults['setting'] ?? [],
            $config['setting'] ?? []
        );

        $packageRoot = dirname(__DIR__);
        $setting = $config['setting'];
        if ($setting['TEMPLATES_PATH'] === null || $setting['TEMPLATES_PATH'] === '') {
            $setting['TEMPLATES_PATH'] = $packageRoot . '/templates';
        }
        if ($setting['PROMPT_PATH'] === null || $setting['PROMPT_PATH'] === '') {
            $setting['PROMPT_PATH'] = $packageRoot . '/prompt';
        }
        $config['setting'] = $setting;

        $this->app['config']->set('modeler', $config);

        $this->app->singleton(BinaryDownloader::class, function () {
            return new BinaryDownloader(new Client([
                'timeout' => 300,
                'connect_timeout' => 30,
            ]));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                StudioCommand::class,
                GenerateCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/modeler.php' => config_path('modeler.php'),
            ], 'modeler-config');
        }

        Relation::enforceMorphMap(config('modeler.morph_map', []));

        $this->loadMigrationsFrom(
            array_map(
                fn($dir) => base_path($dir),
                config('modeler.migration_dirs', ['database/migrations'])
            )
        );
    }
}

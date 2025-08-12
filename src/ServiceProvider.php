<?php

namespace SteadfastCollective\StatamicFeedbucket;

use Statamic\Statamic;
use Statamic\Providers\AddonServiceProvider;
use SteadfastCollective\StatamicFeedbucket\Http\Middleware\ApplyFeedbucketToCP;

class ServiceProvider extends AddonServiceProvider
{
    protected $middlewareGroups = [
        'statamic.cp.authenticated' => [
            ApplyFeedbucketToCP::class,
        ],
    ];

    public function bootAddon()
    {
        $this->registerFieldsets();
        $this->registerGlobalBlueprints();
        $this->registerViews();

        Statamic::afterInstalled(function($command) {
            $command->call('statamic-feedbucket:create-global');
        });
    }

    protected function registerFieldsets(): void
    {
        $this->publishes([
            __DIR__.'/../resources/fieldsets' => resource_path('fieldsets/vendor/statamic-feedbucket'),
        ], 'statamic-feedbucket-fieldsets');
    }

    protected function registerGlobalBlueprints(): void
    {
        $this->publishes([
            __DIR__.'/../resources/blueprints/globals' => resource_path('blueprints/globals'),
        ], 'statamic-feedbucket-blueprints');
    }

    protected function registerViews(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/statamic-feedbucket'),
        ], 'statamic-feedbucket-views');
    }
}

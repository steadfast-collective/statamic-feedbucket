<?php

namespace SteadfastCollective\StatamicFeedbucket;

use Inertia\Inertia;
use Statamic\Statamic;
use Statamic\Facades\Addon;
use Statamic\Addons\Settings;
use Illuminate\Support\Facades\Route;
use Statamic\Providers\AddonServiceProvider;
use SteadfastCollective\StatamicFeedbucket\Http\Middleware\ApplyFeedbucketToCP;
use SteadfastCollective\StatamicFeedbucket\Library\FeedbucketHelpers;

class ServiceProvider extends AddonServiceProvider
{
    protected $middlewareGroups = [
        'statamic.cp.authenticated' => [
            ApplyFeedbucketToCP::class,
        ],
    ];

    protected $vite = [
        'input' => [
            'resources/js/addon.js',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function bootAddon()
    {
        $this->registerFieldsets();
        $this->registerAddonSettings();
        $this->registerViews();
        $this->injectScript();
    }

    protected function registerFieldsets(): void
    {
        $this->publishes([
            __DIR__.'/../resources/fieldsets' => resource_path('fieldsets/vendor/statamic-feedbucket'),
        ], 'statamic-feedbucket-fieldsets');
    }

    protected function registerViews(): void
    {
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/statamic-feedbucket'),
        ], 'statamic-feedbucket-views');
    }

    protected function registerAddonSettings(): void
    {
        $this->registerSettingsBlueprint([
            'tabs' => [
                'main' => [
                    'sections' => [
                        [
                            'display' => 'Feedbucket Settings',
                            'fields' => [
                                [
                                    'import' => 'statamic-feedbucket::global_feedbucket'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    /**
     * Inject the script anyway, regardless of route if enabled_in_cms is true
     * ApplyFeedbucketToCP middleware will pass a prop to the page to determine visibility
     */
    protected function injectScript(): void
    {
        $settings = Addon::get('steadfast-collective/statamic-feedbucket')->settings();

        if(!$settings) {
            return;
        }

        if(!FeedbucketHelpers::shouldEnableFeedbucket($settings, false)) {
            return;
        }

        $feedbucketId = $settings->get('feedbucket_id');

        $script = <<<JS
            (function(k,s) {
                s=document.createElement('script');
                s.module=true;
                s.async=true;
                s.src='https://cdn.feedbucket.app/assets/feedbucket.js';
                s.dataset.feedbucket=k;
                document.head.appendChild(s);
            })('$feedbucketId');

            function onFeedbucketReady(callback) {
                const attempt = () => {
                    const el = document.querySelector('feedbucket-app')

                    if (el?._instance?.exposed) {
                        callback(el)
                    } else {
                        requestAnimationFrame(attempt)
                    }
                }
                attempt()
            }
        JS;

        Statamic::inlineScript($script);
    }

    private function shouldEnableFeedbucket(Settings $feedbucket): bool
    {
        if ((boolean) !$feedbucket->get('enable_in_cms') || !$feedbucket->get('feedbucket_id')) {
            return false;
        }

        // Check if the environment is enabled
        return match (config('app.env')) {
            'local' => (boolean) $feedbucket->get('enabled_environments')['local'],
            'staging' => (boolean) $feedbucket->get('enabled_environments')['staging'],
            'production' => (boolean) $feedbucket->get('enabled_environments')['production'],
            default => false
        };
    }
}

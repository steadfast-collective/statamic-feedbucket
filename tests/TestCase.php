<?php

namespace SteadfastCollective\StatamicFeedbucket\Tests;

use Statamic\Facades\Addon;
use Statamic\Testing\AddonTestCase;
use SteadfastCollective\StatamicFeedbucket\ServiceProvider;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;

    protected function clearStatamicInlineScripts(): void
    {
        if (property_exists(\Statamic\Statamic::class, 'inlineScripts')) {
            $reflection = new \ReflectionClass(\Statamic\Statamic::class);
            $property = $reflection->getProperty('inlineScripts');
            $property->setAccessible(true);
            $property->setValue(null, []);
        }
    }

    protected function setGlobal(array $params = []): void
    {
        $addon = Addon::get('steadfast-collective/statamic-feedbucket');

        $defaultParams = [
            'enable_in_cms' => true,
            'feedbucket_id' => 'abc123',
            'enabled_environments' => [
                'local' => true,
                'staging' => false,
                'production' => false,
            ],
        ];

        $merged = array_merge($defaultParams, $params);

        $addon->settings()->set($merged);
        // $addon->settings()->save();
    }
}

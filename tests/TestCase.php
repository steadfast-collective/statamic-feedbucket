<?php

namespace SteadfastCollective\StatamicFeedbucket\Tests;

use Statamic\Facades\GlobalSet;
use Statamic\Testing\AddonTestCase;
use SteadfastCollective\StatamicFeedbucket\ServiceProvider;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;

    protected $fakeStacheDirectory = __DIR__.'/__fixtures__/dev-null';

    protected function setUp(): void
    {
        parent::setUp();

        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[PreventsSavingStacheItemsToDisk::class])) {
            $this->preventSavingStacheItemsToDisk();
        }
    }

    protected function tearDown(): void
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[PreventsSavingStacheItemsToDisk::class])) {
            $this->deleteFakeStacheDirectory();
        }

        parent::tearDown();
    }

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
        $global = GlobalSet::make('feedbucket');
        $variables = $global->makeLocalization('default');

        $defaultParams = [
            'enable_in_cms' => true,
            'feedbucket_id' => 'abc123',
            'enabled_environments' => [
                'local' => true,
                'staging' => false,
                'production' => false,
            ],
        ];

        $variables->data(array_merge($defaultParams, $params));

        $global->addLocalization($variables);
        $global->save();
    }
}

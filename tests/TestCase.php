<?php

namespace SteadfastCollective\StatamicFeedbucket\Tests;

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
}

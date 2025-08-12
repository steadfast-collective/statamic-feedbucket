<?php

namespace SteadfastCollective\StatamicFeedbucket\Tests;

use SteadfastCollective\StatamicFeedbucket\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}

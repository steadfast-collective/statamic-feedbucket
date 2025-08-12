<?php

namespace SteadfastCollective\StatamicFeedbucket\Tests;

use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class StatamicFeedbucketTest extends TestCase
{
    use PreventsSavingStacheItemsToDisk;

    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}

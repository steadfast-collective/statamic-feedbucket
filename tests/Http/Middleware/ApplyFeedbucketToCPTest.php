<?php

namespace SteadfastCollective\StatamicFeedbucket\Tests\Http\Middleware;

use Statamic\Facades\User;
use SteadfastCollective\StatamicFeedbucket\Tests\TestCase;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class ApplyFeedbucketToCPTest extends TestCase
{
    use PreventsSavingStacheItemsToDisk;

    public string $feedbucketString = "https://cdn.feedbucket.app/assets/feedbucket.js";

    protected function setUp(): void
    {
        parent::setUp();
        $this->clearStatamicInlineScripts();
    }

    public function test_feedbucket_is_included_if_page_is_in_config(): void
    {
        $this->setGlobal();

        $user = User::make()->makeSuper();
        $user->save();
        $this->actingAs($user);

        config([
            'statamic-feedbucket.cms_routes' => ['statamic.cp.dashboard'],
            'app.env' => 'local'
        ]);

        $request = $this->get('/cp/dashboard');

        $request->assertOK();
        $request->assertSee($this->feedbucketString);
    }

    public function test_feedbucket_is_not_included_if_page_is_not_in_config(): void
    {
        $this->setGlobal();

        config([
            'statamic-feedbucket.cms_routes' => ['statamic.cp.dashboard'],
            'app.env' => 'local'
        ]);

        $user = User::make()->makeSuper();
        $user->save();
        $this->actingAs($user);

        $request = $this->get('/cp/collections');

        $request->assertOK();
        $request->assertDontSee($this->feedbucketString);
    }

    public function test_feedbucket_is_included_if_enabled_in_environment(): void
    {
        $this->setGlobal([
            'enabled_environments' => [
                'local' => false,
                'staging' => false,
                'production' => true,
            ],
        ]);

        config([
            'statamic-feedbucket.cms_routes' => ['statamic.cp.dashboard'],
            'app.env' => 'production'
        ]);

        $user = User::make()->makeSuper();
        $user->save();
        $this->actingAs($user);

        $request = $this->get('/cp/dashboard');

        $request->assertOK();
        $request->assertSee($this->feedbucketString);
    }

    public function test_feedbucket_is_not_included_if_disabled_in_environment(): void
    {
        $this->setGlobal([
            'enabled_environments' => [
                'local' => false,
                'staging' => false,
                'production' => false,
            ],
        ]);

        config([
            'statamic-feedbucket.cms_routes' => ['statamic.cp.dashboard'],
            'app.env' => 'local'
        ]);

        $user = User::make()->makeSuper();
        $user->save();
        $this->actingAs($user);

        $request = $this->get('/cp/dashboard');

        $request->assertOK();
        $request->assertDontSee($this->feedbucketString);
    }
}

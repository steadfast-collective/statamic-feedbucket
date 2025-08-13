<?php

namespace SteadfastCollective\StatamicFeedbucket\Tests;

use Statamic\Facades\User;
use Statamic\Facades\Stache;
use Statamic\Facades\GlobalSet;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk;

class StatamicFeedbucketCPTest extends TestCase
{
    use PreventsSavingStacheItemsToDisk;

    public string $feedbucketString = "https://cdn.feedbucket.app/assets/feedbucket.js";

    protected function setUp(): void
    {
        parent::setUp();
        $this->createGlobal();
        // Cache::fake();
        // Stache::fake();
        // Cache::flush();
        // Stache::clear();

        // if (app()->configurationIsCached()) {
        //     \Illuminate\Support\Facades\Artisan::call('config:clear');
        // }
    }

    public function test_feedbucket_is_included_if_page_is_in_config(): void
    {
        config([
            'statamic-feedbucket.cms_routes' => ['statamic.cp.dashboard'],
            'app.env' => 'local'
        ]);

        $user = User::make()->makeSuper();
        $user->save();
        $this->actingAs($user);

        $request = $this->get('/cp/dashboard');

        $request->assertOK();
        $request->assertSee($this->feedbucketString);
    }

    public function test_feedbucket_is_not_included_if_page_is_not_in_config(): void
    {
        config([
            'statamic-feedbucket.cms_routes' => ['statamic.cp.dashboard'],
            'app.env' => 'local'
        ]);

        $user = User::make()->makeSuper();
        $user->save();
        $this->actingAs($user);

        $request = $this->get('/cp/collections');

        ray($request->getContent());

        $request->assertOK();
        $request->assertDontSee($this->feedbucketString);
    }

    private function createGlobal(): void
    {

        $global = GlobalSet::make('feedbucket');
        $variables = $global->makeLocalization('default');

        $variables->data([
            'enable_in_cms' => true,
            'feedbucket_id' => 'abc123',
            'enabled_environments' => [
                'local' => true,
                'staging' => false,
                'production' => false,
            ],
        ]);

        $global->addLocalization($variables);
        $global->save();
    }
}

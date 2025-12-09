<?php

namespace SteadfastCollective\StatamicFeedbucket\Http\Middleware;

use Closure;
use Statamic\Statamic;
use Statamic\Facades\Addon;
use Illuminate\Http\Request;
use Statamic\Addons\Settings;
use Statamic\Facades\GlobalSet;
use Statamic\Globals\Variables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class ApplyFeedbucketToCP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $settings = Addon::get('steadfast-collective/statamic-feedbucket')->settings();

            if (!$settings) {
                Log::error('Feedbucket addon settings not found.');
                return $next($request);
            }

            if($this->shouldEnableFeedbucket($settings)) {
                $this->injectScript($settings->get('feedbucket_id'));
            }

        } catch (\Exception $e) {
            Log::error('Failed to inject Feedbucket into CP: ' . $e->getMessage());
        }

        return $next($request);
    }

    private function shouldEnableFeedbucket(Settings $feedbucket): bool
    {
        if ((boolean) !$feedbucket->get('enable_in_cms') || !$feedbucket->get('feedbucket_id')) {
            return false;
        }

        // Check if current route is in the cms_route in config
        if(!in_array(Route::currentRouteName(), config('statamic-feedbucket.cms_routes'))) {
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

    private function injectScript(string $feedbucketId): void
    {
        $script = <<<JS
            (function(k,s) {
                s=document.createElement('script');
                s.module=true;
                s.async=true;
                s.src='https://cdn.feedbucket.app/assets/feedbucket.js';
                s.dataset.feedbucket=k;
                document.head.appendChild(s);
            })('$feedbucketId');
        JS;

        Statamic::inlineScript($script);
    }
}

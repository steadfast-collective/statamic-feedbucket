<?php

namespace SteadfastCollective\StatamicFeedbucket\Http\Middleware;

use Closure;
use Statamic\Statamic;
use Illuminate\Http\Request;
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
            $feedbucket = GlobalSet::findByHandle('feedbucket')?->inCurrentSite();

            $this->shouldEnableFeedbucket($feedbucket);

            if($feedbucket && $this->shouldEnableFeedbucket($feedbucket)) {
                $this->injectScript($feedbucket->feedbucket_id);
            }

        } catch (\Exception $e) {
            Log::error('Failed to inject Feedbucket into CP: ' . $e->getMessage());
        }

        return $next($request);
    }

    private function shouldEnableFeedbucket(Variables $feedbucket): bool
    {
        if (!$feedbucket->enable_in_cms || !$feedbucket->feedbucket_id) {
            return false;
        }

        // Check if current route is in the cms_route in config
        if(!in_array(Route::currentRouteName(), config('statamic-feedbucket.cms_routes'))) {
            return false;
        }

        // Check if the environment is enabled
        return match (config('app.env')) {
            'local' => $feedbucket->enabled_environments->local,
            'staging' => $feedbucket->enabled_environments->staging,
            'production' => $feedbucket->enabled_environments->production,
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

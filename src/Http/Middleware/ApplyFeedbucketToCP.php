<?php

namespace SteadfastCollective\StatamicFeedbucket\Http\Middleware;

use Closure;
use Inertia\Inertia;
use Statamic\Statamic;
use Statamic\Facades\Addon;
use Illuminate\Http\Request;
use Statamic\Addons\Settings;
use Statamic\Facades\GlobalSet;
use Statamic\Globals\Variables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use SteadfastCollective\StatamicFeedbucket\Library\FeedbucketHelpers;
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

            // Determine visibility
            $this->determineFeedbucketVisibility($settings);

        } catch (\Exception $e) {
            Inertia::share('statamicFeedbucket', function() {
                return [
                    'show' => false
                ];
            });

            Log::error('Failed to inject Feedbucket into CP: ' . $e->getMessage());
        }

        return $next($request);
    }

    protected function determineFeedbucketVisibility(Settings $settings)
    {
        $show = FeedbucketHelpers::shouldEnableFeedbucketInCP($settings);

        Inertia::share('statamicFeedbucket', function() use ($show) {
            return [
                'show' => $show
            ];
        });
    }
}

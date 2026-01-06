<?php

namespace SteadfastCollective\StatamicFeedbucket\Library;

use Statamic\Addons\Settings;
use Illuminate\Support\Facades\Route;

class FeedbucketHelpers
{
    public static function shouldEnableFeedbucketInCP(Settings $feedbucket, bool $routeCheck = true): bool
    {
        if ((boolean) !$feedbucket->get('enable_in_cms') || !$feedbucket->get('feedbucket_id')) {
            return false;
        }

        if($routeCheck) {
            // Check if current route is in the cms_route in config
            if(!in_array(Route::currentRouteName(), config('statamic-feedbucket.cms_routes'))) {
                return false;
            }
        }

        // Check if the environment is enabled
        return match (config('app.env')) {
            'local' => (boolean) $feedbucket->get('enabled_environments')['local'],
            'staging' => (boolean) $feedbucket->get('enabled_environments')['staging'],
            'production' => (boolean) $feedbucket->get('enabled_environments')['production'],
            default => false
        };
    }
}

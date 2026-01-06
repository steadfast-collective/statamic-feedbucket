<?php

namespace SteadfastCollective\StatamicFeedbucket\Tags;

use Statamic\Tags\Tags;
use Statamic\Facades\Addon;

class Feedbucket extends Tags
{
    public function index()
    {
        $settings = Addon::get('steadfast-collective/statamic-feedbucket')->settings();

        $enabled = collect($settings->get('enabled_environments', []))
            ->filter(fn($value, $key) => $key === config('app.env') && $value)
            ->isNotEmpty();

        if(! $enabled) {
            return null;
        }

        return view('statamic-feedbucket::script', [
            'id' => $settings->get('feedbucket_id'),
        ])->render();
    }
}

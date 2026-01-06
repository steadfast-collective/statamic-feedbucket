<?php

namespace SteadfastCollective\StatamicFeedbucket\UpdateScripts;

use Statamic\Facades\Addon;
use Statamic\Facades\GlobalSet;
use Statamic\UpdateScripts\UpdateScript;

class UpdateToUseAddonSettings extends UpdateScript
{
    public function shouldUpdate($newVersion, $oldVersion)
    {
        return $this->isUpdatingTo('2.0.0');
    }

    public function update()
    {
        // Get existing global value
        $global = GlobalSet::findByHandle('feedbucket')->inDefaultSite();

        if(!$global) {
            $this->console()->info('no global found, skipping update.');
        }

        $data = $global ? $global->data() : collect([]);

        if($data->isEmpty()) {
            $this->console()->info('global is empty, skipping update.');
            return;
        }

        try {
            // Migrate existing values to config
            $addon = Addon::get('steadfast-collective/statamic-feedbucket');
            $addon->settings()
                ->set($data->all());

            $addon->settings()->save();
            $this->console()->info('Migrated global data to addon settings');
        } catch (\Exception $e) {
            $this->console()->error('Failed to retrieve global data: ' . $e->getMessage());
            return;
        }
    }
}

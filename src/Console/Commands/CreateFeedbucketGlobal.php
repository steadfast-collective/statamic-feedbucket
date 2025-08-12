<?php

namespace SteadfastCollective\StatamicFeedbucket\Console\Commands;

use Illuminate\Console\Command;
use Statamic\Facades\GlobalSet;

class CreateFeedbucketGlobal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statamic-feedbucket:create-global';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Global for Feedbucket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if the global already exists
        $global = GlobalSet::findByHandle('feedbucket');

        if ($global) {
            $this->error('Global "feedbucket" already exists.');
            return;
        }

        try {
            // Create the global
            $global = GlobalSet::make('feedbucket')
                ->title('Feedbucket');

            $global->save();

            $this->info('Global "feedbucket" created successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to create global: ' . $e->getMessage());
        }
    }
}

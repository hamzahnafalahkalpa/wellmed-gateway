<?php

namespace Projects\WellmedGateway\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class AddTenantJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Artisan::call('db:seed',[
            '--class' => "Projects\WellmedBackbone\\Database\Seeders\\AddDatabaseSeeder"
        ]);   
    }
}

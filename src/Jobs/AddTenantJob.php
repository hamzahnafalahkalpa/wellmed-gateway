<?php

namespace Projects\WellmedGateway\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Hanafalah\LaravelSupport\Jobs\JobRequest;
use Illuminate\Queue\SerializesModels;

class AddTenantJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        JobRequest::set($this->data);      

        Artisan::call('db:seed',[
            '--class' => "Projects\WellmedBackbone\\Database\Seeders\\AddDatabaseSeeder"
        ]);   
    }
}

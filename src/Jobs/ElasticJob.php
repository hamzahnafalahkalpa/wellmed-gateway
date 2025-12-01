<?php

namespace Projects\WellmedGateway\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Projects\WellmedBackbone\Jobs\JobRequest;
use Projects\WellmedGateway\Schemas\Elastic;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Queue\SerializesModels;

class ElasticJob implements ShouldQueue
{
    // use Queueable;
    use Queueable;

    public array $data;
    public $client;

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
        if (config('app.elasticsearch.enabled', false) == false) {
            return;
        }
        $hosts = config('app.elasticsearch.hosts','localhost:9002');
        $this->client = ClientBuilder::create()->setHosts($hosts)
                        ->setApiKey(
                            config('app.elasticsearch.username','elastic'),
                            config('app.elasticsearch.password','password')
                        )
                        ->build();  
        (new Elastic)->run($this->client,$this->data); 
    }
}

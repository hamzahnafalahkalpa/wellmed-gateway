<?php

namespace Projects\WellmedGateway\Schemas;

use Projects\WellmedGateway\Contracts\Schemas\ElasticBulk as SchemasElasticBulk;

class ElasticBulk extends Elastic implements SchemasElasticBulk {
    public function run($client,?array $attributes = null){
        $bulks = [];
        foreach ($attributes['datas'] as $datas) {
            $action = $datas['action'] ?? 'index';

            if ($action === 'delete') {
                // Handle delete action
                $bulks[] = [
                    'delete' => [
                        '_index' => $datas['index'],
                        '_id' => $datas['id']
                    ]
                ];
            } else {
                // Handle index action (default)
                foreach ($datas['data'] as $data) {
                    $bulks[] = [
                        'index' => [
                            '_index' => $datas['index'],
                            '_id' => $data['id']
                        ]
                    ];
                    $bulks[] = $data;
                }
            }
        }
        $this->bulks($bulks);
        $response = $client->bulk($this->__bulks);
    }
}
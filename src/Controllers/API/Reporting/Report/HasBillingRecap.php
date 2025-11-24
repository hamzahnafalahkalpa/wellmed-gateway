<?php

namespace Projects\WellmedGateway\Controllers\API\Reporting\Report;

trait HasBillingRecap{
    public function billingRecap(){
        // $query_params 
        $response = &$this->__response;
        $patients = $this->__client->search([
            'index' => config('app.elasticsearch.indexes.billing.full_name'),
            'body'  => [
                'from' => $response['from'],
                'size' => $response['per_page']
                // 'query' => [
                //     "bool" => [
                //         "must" => [
                //             [
                //                 "wildcard" => [
                //                     "name" => "hamzah*"
                //                 ]
                //             ]
                //         ]
                //     ]
                // ]
            ]
        ]);
        $this->resolveForPaginate($response,$patients);
        $response['columns'] = [
            ["key" => "reported_at", "label" => "Tanggal Laporan"],
            ["key" => "billing_code", "label" => "Kode Billing"],
            ["key" => "has_transaction.consument.reference.medical_record", "label" => "No RM"],
            ["key" => "has_transaction.consument.reference.people.card_identity.nik", "label" => "NIK"],
            ["key" => "has_transaction.consument.name", "label" => "Nama Pasien"],
            ["key" => "author.name", "label" => "Petugas"],
            ["key" => "amount", "label" => "Total Tagihan"],
            ["key" => "debt", "label" => "Sisa Tagihan"]
        ];
        $response['filters'] = [
            [
            'label'          => 'Tanggal Laporan',
            'key'            => 'reported_at',
            'type'           => 'InputText',
            'component_name' => null,
            'default_value'  => null,
            'attribute'      => null,
            'options'        => []
            ],
            [
            'label'          => 'Kode Billing',
            'key'            => 'billing_code',
            'type'           => 'InputText',
            'component_name' => null,
            'default_value'  => null,
            'attribute'      => null,
            'options'        => []
            ],
            [
            'label'          => 'No. RM',
            'key'            => 'has_transaction.consument.reference.medical_record',
            'type'           => 'InputText',
            'component_name' => null,
            'default_value'  => null,
            'attribute'      => null,
            'options'        => []
            ],
            [
            'label'          => 'NIK',
            'key'            => 'has_transaction.consument.reference.people.card_identity.nik',
            'type'           => 'InputText',
            'component_name' => null,
            'default_value'  => null,
            'attribute'      => null,
            'options'        => []
            ],
            [
            'label'          => 'Nama Pasien',
            'key'            => 'has_transaction.consument.name',
            'type'           => 'InputText',
            'component_name' => null,
            'default_value'  => null,
            'attribute'      => null,
            'options'        => []
            ],
            [
            'label'          => 'Petugas',
            'key'            => 'author.name',
            'type'           => 'InputText',
            'component_name' => null,
            'default_value'  => null,
            'attribute'      => null,
            'options'        => []
            ],
            [
            'label'          => 'Total Tagihan',
            'key'            => 'amount',
            'type'           => 'InputText',
            'component_name' => null,
            'default_value'  => null,
            'attribute'      => null,
            'options'        => []
            ],
            [
            'label'          => 'Sisa Tagihan',
            'key'            => 'debt',
            'type'           => 'InputText',
            'component_name' => null,
            'default_value'  => null,
            'attribute'      => null,
            'options'        => []
            ],
            [
            'label'          => 'Total Bayar',
            'key'            => 'invoices.paid',
            'type'           => 'InputText',
            'component_name' => null,
            'default_value'  => null,
            'attribute'      => null,
            'options'        => []
            ]
        ];
        return $response;
    }
}
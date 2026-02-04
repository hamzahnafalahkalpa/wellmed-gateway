<?php

namespace Projects\WellmedGateway\Controllers\API\Reporting\Report;
use Illuminate\Support\Str;

trait HasPatientRecap{
    public function patientRecap(){
        // $query_params 
        $response = &$this->__response;
        $search = [
            'index' => config('app.elasticsearch.indexes.patient.full_name'),
            'body'  => [
                'from' => $response['from'],
                'size' => $response['per_page']
            ]
        ];
        $response['filters'] = [
            [
                'label'          => 'No. RM',
                'key'            => 'medical_record',
                'type'           => 'InputText',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => [
                ]
            ],
            [
                'label'          => 'NIK',
                'key'            => 'people.card_identity.nik',
                'type'           => 'InputText',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => [
                ]
            ],
            [
                'label'          => 'Nama Pasien',
                'key'            => 'name',
                'type'           => 'InputText',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => [
                ]
            ],
            // [
            //     'label'          => 'Jenis Pasien',
            //     'key'            => 'patient_type.name',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => [
            //     ]
            // ],
            // [
            //     'label'          => 'Kontak 1',
            //     'key'            => 'people.phone_1',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => [
            //     ]
            // ],
            // [
            //     'label'          => 'Kontak 2',
            //     'key'            => 'people.phone_2',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => [
            //     ]
            // ],
            // [
            //     'label'          => 'Usia',
            //     'key'            => 'people.age',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => [
            //     ]
            // ],
            // [
            //     'label'          => 'Jenis Kelamin',
            //     'key'            => 'people.sex',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => [
            //     ]
            // ],
            [
                'label'          => 'Tempat Lahir',
                'key'            => 'people.pob',
                'type'           => 'InputText',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => [
                ]
            ],
            [
                'label'          => 'Tanggal Lahir',
                'key'            => 'people.dob',
                'type'           => 'InputText',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => [
                ]
            ]
        ];
        $this->handleQueryParams($search,$response['filters']);
        $patients = $this->__client->search($search);
        $this->resolveForPaginate($response,$patients);
        $response['columns'] = [
            ["key"     => "medical_record","label" => "No RM"],
            ["key"     => "people.card_identity.nik","label"   => "NIK"],
            ["key"     => "name","label"   => "Nama Pasien"],
            ["key"     => "patient_type.name","label"   => "Jenis Pasien"],
            ["key"     => "people.phone_1","label"   => "Kontak 1"],
            ["key"     => "people.phone_2","label"   => "Kontak 2"],
            ["key"     => "people.age","label"   => "Usia"],
            ["key"     => "people.sex","label"   => "Jenis Kelamin"],
            ["key"     => "people.pob","label"   => "Tempat Lahir"],
            ["key"     => "people.dob","label"   => "Tanggal Lahir"]
        ];
        return $response;
    }
}
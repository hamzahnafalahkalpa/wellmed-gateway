<?php

namespace Projects\WellmedGateway\Controllers\API\Reporting\Report;

trait HasVisitPatientRecap{
    public function visitPatientRecap(){
        // $query_params 
        $response = &$this->__response;
        $search = [
            'index' => config('app.elasticsearch.indexes.visit_patient.full_name'),
            'body'  => [
                'from' => $response['from'],
                'size' => $response['per_page']                
            ]
        ];
        $response['filters'] = [
            [
                'label'          => 'Tanggal Berkunjung',
                'key'            => 'visited_at',
                'type'           => 'DateRangePicker',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => []
            ],
            // [
            //     'label'          => 'Kode Kunjungan',
            //     'key'            => 'visit_code',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ],
            // [
            //     'label'          => 'Petugas Pendaftaran',
            //     'key'            => 'practitioner_evaluation.name',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ],
            // [
            //     'label'          => 'No. RM',
            //     'key'            => 'medical_record',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ],
            [
                'label'          => 'NIK',
                'key'            => 'people.card_identity.nik',
                'type'           => 'InputText',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => []
            ],
            [
                'label'          => 'Nama Pasien',
                'key'            => 'name',
                'type'           => 'InputText',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => []
            ],
            // [
            //     'label'          => 'Jenis Pasien',
            //     'key'            => 'patient_type.name',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ],
            // [
            //     'label'          => 'Kontak 1',
            //     'key'            => 'people.phone_1',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ],
            // [
            //     'label'          => 'Kontak 2',
            //     'key'            => 'people.phone_2',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ],
            // [
            //     'label'          => 'Usia',
            //     'key'            => 'people.age',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ],
            // [
            //     'label'          => 'Jenis Kelamin',
            //     'key'            => 'people.gender',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ],
            // [
            //     'label'          => 'Tempat Lahir',
            //     'key'            => 'people.pob',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ],
            // [
            //     'label'          => 'Tanggal Lahir',
            //     'key'            => 'people.dob',
            //     'type'           => 'InputText',
            //     'component_name' => null,
            //     'default_value'  => null,
            //     'attribute'      => null,
            //     'options'        => []
            // ]
        ];
        $this->handleQueryParams($search,$response['filters']);
        $search = $this->__client->search($search);
        $this->resolveForPaginate($response,$search);
        $response['columns'] = [
            ["key" => "visited_at", "label" => "Tanggal Berkunjung"],
            ["key" => "visit_code", "label" => "Kode Kunjungan"],
            ["key" => "practitioner_evaluation.name", "label" => "Petugas Pendaftaran"],
            ["key" => "patient.medical_record", "label" => "No RM"],
            ["key" => "patient.people.card_identity.nik", "label" => "NIK"],
            ["key" => "patient.people.name", "label" => "Nama Pasien"],
            ["key" => "patient_type.name", "label" => "Jenis Pasien"],
            ["key" => "patient.people.phone_1", "label" => "Kontak 1"],
            ["key" => "patient.people.phone_2", "label" => "Kontak 2"],
            ["key" => "patient.people.age", "label" => "Usia"],
            ["key" => "patient.people.gender", "label" => "Jenis Kelamin"],
            ["key" => "patient.people.pob", "label" => "Tempat Lahir"],
            ["key" => "patient.people.dob", "label" => "Tanggal Lahir"]
        ];
        return $response;
    }
}
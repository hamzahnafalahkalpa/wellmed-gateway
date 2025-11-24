<?php

namespace Projects\WellmedGateway\Controllers\API\Reporting\Report;

trait HasMedicObservationRecap{
    public function medicObservationRecap(){
        // $query_params 
        $response = &$this->__response;
        $search = $this->__client->search([
            'index' => config('app.elasticsearch.indexes.patient_illness.full_name'),
            'body'  => [
                'from' => $response['from'],
                'size' => $response['per_page']                
            ]
        ]);
                $response['filters'] = [
            [
                'label'          => 'Nama Diagnosis',
                'key'            => 'disease_name',
                'type'           => 'InputText',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => []
            ],
            [
                'label'          => 'Nama Pasien',
                'key'            => 'patient.name',
                'type'           => 'InputText',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => []
            ],
            [
                'label'          => 'Jenis Kelamin',
                'key'            => 'patient.people.sex',
                'type'           => 'Select',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => [
                    ['label' => 'Laki-laki', 'value' => 'Male'],
                    ['label' => 'Perempuan', 'value' => 'Female']
                ]
            ],
            [
                'label'          => 'Tanggal Dibuat',
                'key'            => 'created_at',
                'type'           => 'DateRange',
                'component_name' => null,
                'default_value'  => null,
                'attribute'      => null,
                'options'        => []
            ]
        ];
        $this->handleQueryParams($search,$response['filters']);
        $this->resolveForPaginate($response,$search);
        $response['columns'] = [
            ["key" => "name", "label" => "Nama Diagnosis"],
            ["key" => "disease.code", "label" => "Kode Diagnosis"],
            ["key" => "classification_disease.name", "label" => "Klasifikasi Penyakit"],
            ["key" => "classification_disease.code", "label" => "Kode Klasifikasi Penyakit"],
            ["key" => "patient.name", "label" => "Nama Pasien"],
            ["key" => "patient.people.sex", "label" => "Jenis Kelamin"],
            ["key" => "patient.people.dob", "label" => "Tanggal Lahir"],
            // ["key" => "reference.practitioner_evaluations", "label" => "Dokter"],
            ["key" => "created_at", "label" => "Tanggal Dibuat"]
        ];

        return $response;
    }
}
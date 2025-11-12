<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Patient;

class StoreRequest extends PatientEnvironment
{
    protected $__entity = 'Patient';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "card_identity"                                        => ["nullable","array"],
            "medical_record"                                       => ["nullable","string"],
            "card_identity.old_medical_record"                     => ["nullable","string"],
            "card_identity.ihs_number"                             => ["nullable","string"],
            "card_identity.bpjs"                                   => ["nullable","string"],
            "people.first_name"                                    => ["nullable","string","max:50"],
            "people.last_name"                                     => ["required","string","max:50"],
            "people.pob"                                           => ["required","string"],
            "people.dob"                                           => ["required","date","date_format:Y-m-d"],
            "people.last_education"                                => ["nullable"],
            "people.country_id"                                    => ["nullable","integer"],
            "people.sex"                                           => ["nullable","string","in:Male,Female"],
            "people.card_identity"                                 => ['nullable','array'],
            "people.card_identity.nik"                             => ['nullable','string','min:16','max:16'],
            "people.card_identity.kk"                              => ['nullable','string'],
            "people.card_identity.sim"                             => ['nullable','string'],
            "people.card_identity.passport"                        => ['nullable','string'],
            "people.family_relationship"                           => ["nullable","array"],
            "people.family_relationship.role"                      => ["nullable","numeric"],
            "people.family_relationship.name"                      => ["nullable","string"],
            "people.family_relationship.phone"                     => ["nullable","numeric"],
            "people.address.ktp_address"                           => ["nullable","array"],
            "people.address.ktp_address.name"                      => ["nullable","string"],
            "people.address.ktp_address.rt"                        => ["nullable","string","max:3"],
            "people.address.ktp_address.rw"                        => ["nullable","string","max:3"],
            "people.address.ktp_address.province_id"               => ["nullable","numeric"],
            "people.address.ktp_address.district_id"               => ["nullable","numeric"],
            "people.address.ktp_address.subdistrict_id"            => ["nullable","numeric"],
            "people.address.ktp_address.village_id"                => ["nullable","numeric"],
            "people.address.residence_same_ktp"                    => ["nullable","boolean"],
            "people.address.residence_address"                     => ["nullable","array"],
            "people.address.residence_address.name"                => ["nullable","string"],
            "people.address.residence_address.rt"                  => ["nullable","string","max:3"],
            "people.address.residence_address.rw"                  => ["nullable","string","max:3"],
            "people.address.residence_address.province_id"         => ["nullable","numeric"],
            "people.address.residence_address.district_id"         => ["nullable","numeric"],
            "people.address.residence_address.subdistrict_id"      => ["nullable","numeric"],
            "people.address.residence_address.village_id"          => ["nullable","numeric"]
        ];
    }
}

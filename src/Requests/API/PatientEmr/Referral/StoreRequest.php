<?php

namespace Projects\WellmedGateway\Requests\API\PatientEmr\Referral;

class StoreRequest extends EnvironmentRequest
{
    public function authorize()
    {
        return true;
        if (isset(request()->id)){
            $referral = $this->usingEntity()->findOrFail(request()->id);
            return $referral->status == 'CREATED';
        }
        return false;
    }

    public function rules()
    {
        return [
        ];
    }
}

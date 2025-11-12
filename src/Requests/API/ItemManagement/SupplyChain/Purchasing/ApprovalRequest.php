<?php

namespace Projects\WellmedGateway\Requests\API\ItemManagement\SupplyChain\Purchasing;

use Hanafalah\LaravelSupport\Requests\FormRequest;
use Illuminate\Support\Str;

class ApprovalRequest extends FormRequest
{
  public function authorize()
  {
      $this->userAttempt();
      $purchasing = $this->usingEntity()->findOrFail(request()->id);
      $approval   = $purchasing->approval;
      if ($approval['status'] == 'CANCELED') return false;
      $approver      = $approval['approver'];
      $approver_keys = array_keys($approver);
      $occupation_name = $this->global_employee->prop_occupation['label'];
      if (isset($occupation_name)){
          $occupation_name = Str::lower($occupation_name);
          if (in_array($occupation_name,['Kepala Keuangan'])){
              return true;
          }else{
              if (in_array($occupation_name.'_id',$approver_keys)){
                  return !isset($approver[$occupation_name.'_id']);
              }
          }
      }
      return false;
  }

  public function rules()
  {
    return [];
  }
}
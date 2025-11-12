<?php

namespace Projects\WellmedGateway\Concerns;

use Hanafalah\ApiHelper\Facades\ApiAccess;

trait HasUser
{
    public $global_user;
    public $global_employee;
    public $global_room;
    public $global_user_reference;
    public $global_workspace;

    public function userAttempt()
    {
        $user = ApiAccess::getUser();
        $this->global_user = $user;
        if (isset($user)){
            $user->load([
                'userReference' => function($query){
                    $query->with([
                        'role', 'reference',
                        'workspace'
                    ])->where('reference_type', $this->EmployeeModelMorph());
                }
            ]);
            $user_reference = $user->userReference;
            if (isset($user_reference)){
                $this->global_user_reference = &$user_reference;
    
                if ($user_reference->reference_type == $this->EmployeeModelMorph()){
                    $this->global_employee = $user_reference->reference;
                    $this->global_employee->load('profession','room');
                    if (isset($this->global_employee->room)) $this->global_room = $this->global_employee->room;
                }
    
                $tenant = $user_reference->workspace;
                if(isset($tenant)) {
                    $workspace = $tenant->reference;
                    $this->global_workspace = $workspace;
    
                    if (isset($workspace->setting)){
                        $setting = $workspace->setting;
                    }
                }
                $impersonate = config()->get('app.impersonate');
                config()->set('app.impersonate', $this->mergeArray($impersonate,[
                    'auth'      => $user,
                    'workspace' => $workspace ?? null,
                ]));
            }
        }
        parent::__construct();
    }
}
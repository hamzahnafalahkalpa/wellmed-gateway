<?php

namespace Projects\WellmedGateway\Controllers\API\Tenant;

use Projects\WellmedGateway\Jobs\AddTenantJob;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Http\Request;

class AddTenantController extends ApiController{
    public function store(Request $request){
        try {
            $data = request()->all();
            // $data = [
            //     'workspace_id' => '01kbk9kde1ap43xzmrw87qys8s',
            //     'workspace_name' => 'Klinik Wellmed',
            //     'product_label' => 'LITE',
            //     'group_tenant_id' => 3,
            //     'app_tenant_id' => 2,
            //     'admin' => [
            //         "id"=> null,
            //         "name"=> "admin_wellmed",
            //         "username"=> "admin_wellmed",
            //         "email"=> "admin_wellmed@mail.com",
            //         "password"=> "password",
            //         "password_confirmation"=> "password"
            //     ]
            // ];
            dispatch(new AddTenantJob($data))->onQueue('installation')->onConnection('rabbitmq');
        } catch (\Throwable $th) {
            dd($th->getMessage());
            throw $th;
        }
        return response()->json([
            'message' => 'Seeder sedang dijalankan di background'
        ]);
    }
}
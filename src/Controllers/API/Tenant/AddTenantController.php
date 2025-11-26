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
            //     'workspace_id' => '01kax74158mp5hta48kwt60ffh',
            //     'workspace_name' => 'vvvvv',
            //     'product_label' => 'LITE',
            //     'group_tenant_id' => 6,
            //     'app_tenant_id' => 5,
            //     'admin' => [
            //         "id"=> null,
            //         "name"=> "admin_vvv",
            //         "username"=> "vvvvad",
            //         "email"=> "adminvvv@mail.com",
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
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
            //     "workspace_id" => "01kbks33epxgk8xxgbdbh0nqdq",
            //     "workspace_name" => "Klinik Wellmed Kedua",
            //     "product_label" => "LITE",
            //     "app_tenant_id" => 2,
            //     "group_tenant_id" => 3,
            //     "admin" => [
            //         "id" => null,
            //         "name" => "admin_wellmed",
            //         "username" => "admin_wellmed",
            //         "email" => "admin_wellmed@mail.com",
            //         "password" => "password",
            //         "password_confirmation" => "password"
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
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
            //     'workspace_id' => '01kawvy0cngm65jdqj61zvzpcb',
            //     'workspace_name' => 'Wellmed h 9',
            //     'product_label' => 'LITE',
            //     'group_tenant_id' => 6,
            //     'app_tenant_id' => 5
            // ];
            dispatch(new AddTenantJob($data))->onQueue('installation')->onConnection('rabbitmq');
        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json([
            'message' => 'Seeder sedang dijalankan di background'
        ]);
    }
}
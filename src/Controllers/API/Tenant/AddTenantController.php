<?php

namespace Projects\WellmedGateway\Controllers\API\Tenant;

use Projects\WellmedGateway\Jobs\AddTenantJob;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Http\Request;

class AddTenantController extends ApiController{
    public function store(Request $request){
        try {
            $data = request()->all();
            dispatch(new AddTenantJob($data))->onQueue('installation')->onConnection('rabbitmq');
        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json([
            'message' => 'Seeder sedang dijalankan di background'
        ]);
    }
}
<?php

namespace Projects\WellmedGateway\Controllers\API\Tenant;

use Projects\WellmedGateway\Jobs\AddTenantJob;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class AddTenantController extends ApiController{
    public function store(Request $request){
        try {
            //code...
            dispatch(new AddTenantJob())->onQueue('installation');
        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json([
            'message' => 'Seeder sedang dijalankan di background'
        ]);

        // $process = new Process([
        //     'php', base_path('artisan'),
        //     'db:seed',
        //     '--class=Projects\\WellmedBackbone\\Database\\Seeders\\AddDatabaseSeeder'
        // ]);

        // $process->start(function ($type, $data) {
        //     if ($type === Process::ERR) {
        //         \Log::error("Seeder error: " . $data);
        //     } else {
        //         \Log::info("Seeder output: " . $data);
        //     }
        // });

        // return response()->json([
        //     'message' => 'Seeder running in background'
        // ]);
    }
}
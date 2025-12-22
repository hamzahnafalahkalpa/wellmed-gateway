<?php

namespace Projects\WellmedGateway\Controllers\API\Import;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Projects\WellmedBackbone\Jobs\PatientImportJob;

class ImportController extends ApiController{
    public function store(Request $request){
        $data = request()->all();
        try {
            switch (request()->type) {
                case 'patient':
                    dispatch(new PatientImportJob($data))->onQueue('import')->onConnection('sync');
                break;
                default:
                    return response()->json([
                        'message' => 'Import type not supported'
                    ], 400);
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
            throw $th;
        }
        return response()->json([
            'message' => 'Import sedang dijalankan di background'
        ]);
    }
}
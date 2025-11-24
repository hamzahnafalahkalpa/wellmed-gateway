<?php

namespace Projects\WellmedGateway\Controllers\API\Xendit;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Http\Request;

class XenditController extends ApiController{
    public function index(Request $request){
        \Log::channel('xendit')->info('Xendit paid callback', [
            'payload' => request()->all(),
            'headers' => request()->headers->all()
        ]);
        return response()->json([
            'message' => 'Received',
            'payload' => request()->all(),
            'headers' => request()->headers->all()
        ]);
    }
}
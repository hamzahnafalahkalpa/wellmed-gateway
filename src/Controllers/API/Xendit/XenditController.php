<?php

namespace Projects\WellmedGateway\Controllers\API\Xendit;

use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Http\Request;

class XenditController extends ApiController{
    public function store(Request $request){
        \Log::channel('xendit')->info('Backbone: Xendit paid callback', [
            'payload' => request()->all(),
            'headers' => request()->headers->all()
        ]);
        $hqUrl = config('wellmed-backbone.hq.url');
        $client = new \GuzzleHttp\Client();
        try {
            $headers = request()->headers->all();
            $headers['appcode'] = 3;
            $client->post($hqUrl . '/api/xendit/paid', [
                'headers' => $headers,
                'json' => request()->all()
            ]);
        } catch (\Exception $e) {
            \Log::channel('xendit')->error('Backbone: Failed to forward to HQ', [
                'error' => $e->getMessage()
            ]);
        }
        return response()->json([
            'message' => 'Received',
            'payload' => request()->all(),
            'headers' => request()->headers->all()
        ]);
    }
}
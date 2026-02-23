<?php

namespace Projects\WellmedGateway\Controllers\API\Setting;

use Hanafalah\ModuleWorkspace\Contracts\Schemas\Workspace;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Projects\WellmedGateway\Requests\API\Setting\Workspace\{
    ShowRequest, StoreRequest
};

class WorkspaceController extends ApiController{
    public function __construct(
        protected Workspace $__workspace_schema
    ){
        parent::__construct();
    }

    public function show(ShowRequest $request){
        return $this->__workspace_schema->showWorkspace();
    }

    public function store(StoreRequest $request){
        return $this->__workspace_schema->storeWorkspace();
    }

    public function storeLogo(Request $request){
        $request->validate([
            'uuid' => 'required|string',
            'logo' => 'required', // Can be file upload or base64 string
        ]);

        return $this->transaction(function(){
            $workspace = $this->WorkspaceModel()->where('uuid', request()->uuid)->firstOrFail();

            // Get current setting (convert to array if object)
            $setting = $workspace->setting;
            if (is_object($setting)) {
                $setting = json_decode(json_encode($setting), true);
            }
            $setting = $setting ?? [];

            // Upload logo using HasFileUpload trait
            // setupFile() handles UploadedFile, base64, or URL string
            $logoPath = $workspace->setupFile(
                file: request()->file('logo') ?? request()->input('logo'),
                path: null // Uses default path from getFilePath() -> 'WORKSPACES/{uuid}'
            );

            // Update setting with new logo path
            if ($logoPath) {
                $setting['logo'] = $logoPath;
                $workspace->setAttribute('setting', $setting);
                $workspace->save();
            }

            return $workspace->toShowApi()->resolve();
        });
    }
}
<?php

namespace Projects\WellmedGateway\Controllers\API\ItemManagement\Item;

use Hanafalah\ModuleItem\Contracts\Schemas\Item;
use Projects\WellmedGateway\Controllers\API\ApiController;
use Hanafalah\ModuleSupport\Contracts\Schemas\Support;
use Projects\WellmedGateway\Requests\API\ItemManagement\Item\{
    DeleteRequest, StoreRequest, ViewRequest, ShowRequest
};
use Illuminate\Http\Request;
use Aws\S3\S3Client;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ItemController extends ApiController{
    public function __construct(
        protected Item $__schema,    
        protected Support $__support_schema    
    ){
        parent::__construct();
    }

    public function index(ViewRequest $request) {
        // $flag = request()->flag ?? [
        //     $this->MedicineModelMorph(),
        //     $this->MedicToolModelMorph()
        // ];
        // request()->merge([
        //     'flag' => $flag
        // ]);
        return $this->__schema->viewItemPaginate();
    }

    public function store(StoreRequest $request){
        request()->merge([
            'reference' => [
                'id' => request()->reference_id ?? null,
                'name' => request()->name ?? '',
            ]
        ]);
        return $this->__schema->storeItem();
    }

    public function show(ShowRequest $request){
        return $this->__schema->showItem();
    }

    public function destroy(DeleteRequest $request){
        return $this->__schema->deleteItem();
    }

    public function init(Request $request){
        $this->userAttempt();
        request()->merge([
            'name' => 'Import Item',
            'reference_type' => 'Workspace',
            'reference_id' => $this->global_workspace->getKey(),
            'author_type' => 'Employee',
            'author_id' => $this->global_employee->getKey(),
        ]);
        if (isset(request()->total_size)){
            request()->merge([
                'chunk_size' => 5 * 1024 * 1024, //10 MB
                'name' => 'Upload file '.request()->filename,
                'is_chunk' => true
            ]);
        }
        $uuid    = Str::orderedUuid()->toString();
        $target_key = "support/{$uuid}";
        $is_prisigned = request()->is_prisigned;
        if (isset($is_prisigned)){
            $is_prisigned = filter_var($is_prisigned, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }
        if (isset($is_prisigned) && $is_prisigned){
            $target_key .= "/".request()->filename;
            $s3Client = new S3Client([
                'version'     => 'latest',
                'region'      => config('filesystems.disks.s3.region'),
                'credentials' => [
                    'key'    => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
            ]);

            $bucket = config('filesystems.disks.s3.bucket');

            $result = $s3Client->createMultipartUpload([
                'Bucket' => $bucket,
                // 'Key'    => $key,
                'Key'    => $target_key,
                'ACL'    => 'private'
            ]);

            $uploadId = $result['UploadId'];
            $totalSize   = (int) request()->total_size;
            $chunkSize   = (int) request()->chunk_size;
            $totalChunks = (int) ceil($totalSize / $chunkSize);
            $chunks = [];
            for ($partNumber = 1; $partNumber <= $totalChunks; $partNumber++) {
                $command = $s3Client->getCommand('UploadPart', [
                    'Bucket'     => $bucket,
                    'Key'        => $target_key,
                    'UploadId'   => $uploadId,
                    'PartNumber' => $partNumber,
                ]);

                $presignedRequest = $s3Client->createPresignedRequest($command, '+1 hour');
                $chunks[] = [
                    'part_number' => $partNumber,
                    'key'         => $target_key,
                    'url'         => (string) $presignedRequest->getUri(),
                ];
            }

            request()->merge([
                'upload_id'    => $uploadId,
                'target_path'  => $target_key,
                'is_presigned' => true,
                'chunks'      => $chunks,
            ]);
        }else{
            if (!isset(request()->file)) throw new Exception("File is required", 1);

            request()->merge([
                'files' => [
                    request()->file('file')
                ],
                'target_path'  => $target_key,
                'file' => null
            ]);
        }
        return $this->__support_schema->storeSupport();
    }
    
    public function uploadComplete(Request $request){
        $support = $this->SupportModel()->findOrFail(request()->import_id);
        if (isset(request()->etags) && count(request()->etags) > 0){
            $etags = request()->etags;
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.s3.region'),
                'credentials' => [
                    'key'    => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
            ]);
    
            $parts = [];
            foreach ($etags as $partNumber => $etag) {
                $parts[] = [
                    'ETag' => trim($etag, '"'), // hapus tanda kutip
                    'PartNumber' => (int) $partNumber + 1, // partNumber dimulai dari 1
                ];
            }
            $bucket = config('filesystems.disks.s3.bucket');
            $result = $s3Client->completeMultipartUpload([
                'Bucket' => $bucket,
                'Key'    => $support->target_path,       // path file final di S3
                'UploadId' => $support->upload_id,
                'MultipartUpload' => [
                    'Parts' => $parts
                ],
            ]);
            $file_url = $result['Location'];
            return response()->json([
                'message' => 'Import running.',
                'file_url' => $result['Location'],
            ]);
        }else{
            $file_url = $support->paths[0] ?? null;
        }
        $support->setAttribute('paths',[$file_url]);
        $support->save();

        $headers = request()->headers->all();
        unset($headers['content-type']);
        $url = config('wellmed-backbone.listener.url',null);
        // $url = 'http://host.docker.internal:9000';
        $url = rtrim($url,'/').'/api/item-management/item/import/process';
        if (!isset($url)) throw new \Exception('Wellmed Backbone Listener URL is not configured');

        // request()->merge([
        //     'support' => $support->toViewApi()->resolve(),
        // ]);
        // try {
        //     $this->import(request());
        // } catch (\Throwable $th) {
        //     dd($th->getMessage());
        //     //throw $th;
        // }

        try {
            $response = Http::withHeaders(array_merge($headers,[
                'Accept' => '*/*'
            ]))
            ->timeout(10)
            ->post($url, ['connections' => config('database.connections'),'support' => $support->toViewApi()->resolve()]);
            if ($response->failed()) {
                throw new \RuntimeException(
                    "Backbone Listener API call failed with status {$response->status()}: {$response->body()}"
                );
            }
        } catch (\Throwable $th) {
            throw $th;
        }
        return response()->json([
            'message' => 'Import running.',
            'file_url' => $file_url
        ]);
    }

    public function import(Request $request){
        $this->userAttempt();
        \Log::channel('import')->info("Tenant ID: ".tenancy()->tenant->id);
        if (!isset(request()->files)){
            request()->merge([
                'files' => [
                    $request->file('file')
                ]
            ]);
            $attributes = request()->all();
            unset($attributes['file']);
        }else{
            $attributes = request()->all();
        }
        $attributes['tenant_id'] = tenancy()->tenant->id;
        return $this->__schema->import('Item')->handle($attributes);
    }

    public function downloadTemplate(Request $request){
        return redirect()->away(
            backbone_asset('assets/item-data.xlsx')
        );
        return response()->download(backbone_asset('assets/item-data.xlsx'));
    }

    public function uploadChunk(Request $request){
        ini_set('memory_limit', '1G');
        $support = $this->SupportModel()->findOrFail(request()->import_id);
        $support_chunks = $support->chunks;
        $chunks = [];
        foreach ($support_chunks as $key => $chunk) {
            $chunks[] = [
                'part_number' => $chunk['part_number'],
                'url' => $chunk['url'],
                'file_index' => $key
            ];
        }
        $etags = [];
        foreach ($chunks as $chunk) {
            $file = $request->file("files.".$chunk['file_index']);
            if (!$file || !$file->isValid()) {
                throw new \Exception("File chunk #{$chunk['part_number']} invalid ".$file->getErrorMessage());
            }

            $etags[] = $this->uploadChunkProcess($file->getPathname(), [
                'url' => $chunk['url'],
                'part_number' => $chunk['part_number'],
            ]);
        }

        return response()->json([
            'etags' => $etags
        ]);
    }

    public function uploadChunkProcess(string $filePath, array $data){
        $fileStream = fopen($filePath, 'rb'); // buka stream
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/octet-stream',
            ])->withBody($fileStream, 'application/octet-stream')
            ->put($data['url']);

            if ($response->failed()) {
                throw new \Exception("Gagal upload chunk #{$data['part_number']} ke S3");
            }

            return trim($response->header('ETag'), '"');
        } finally {
            fclose($fileStream); // pastiin stream ditutup
        }
    }
}

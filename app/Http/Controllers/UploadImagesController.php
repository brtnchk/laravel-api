<?php

namespace App\Http\Controllers;

use App\Http\Requests\Image\CreateRequest;
use App\Services\FilesService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;


class UploadImagesController extends Controller
{
    /**
     * Upload image
     *
     * @param CreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *   path="/api/upload",
     *   summary="Upload image",
     *   operationId="uploadImage",
     *   tags={"Images"},
     *   @SWG\Parameter(
     *     name="image",
     *     in="formData",
     *     description="Image",
     *     required=true,
     *     type="file"
     *   ),
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="An authorization header",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     *
     */
    public function create(CreateRequest $request)
    {
        $data = FilesService::storeImage($request);
        return response()->json(['filename' => $data], 201);
    }

    /**
     * Display the image.
     *
     * @param string $fileName
     * @return string
     *
     * @SWG\Get(
     *   path="/api/upload/{filename}",
     *   summary="Show the note's item",
     *   operationId="api.images.show",
     *   tags={"Images"},
     *   @SWG\Parameter(
     *     name="filename",
     *     in="path",
     *     description="Filename",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response="default", description="successful operation")
     * )
     */
    public function show(string $fileName)
    {
        $folder = Config::get('filesystems.images_folder');
        return Storage::disk('s3')->url("$folder/$fileName");
    }
}

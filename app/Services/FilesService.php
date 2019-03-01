<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as Img;
use App\Models\Image;

class FilesService
{
    /**
     * save image
     *
     * @param $request
     * @return mixed
     */
    public static function storeImage($request)
    {
        $fileName = '';
        $folder = Config::get('filesystems.images_folder');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = self::storeFile($folder, $file);
            Image::create([
                'filename' => basename($fileName),
                'user_id' => auth()->id()
            ]);
        }

        return basename($fileName);
    }

    /**
     * store file on AWS
     *
     * @param $folder
     * @param $file
     * @return string
     */
    public static function storeFile($folder, $file)
    {
        $fileName = $file->hashName();
        $filePath = $folder . '/' . $fileName;
        $img = Img::make($file);

        Storage::disk('s3')->put($filePath, file_get_contents($file), [
            'Metadata' => [
                'width'  => $img->width(),
                'height' => $img->height()
            ]
        ]);

        return $fileName;
    }
}

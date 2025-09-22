<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait MediaUploadingTrait
{
    public function storeMedia(Request $request)
    {
        // Validates file size
        if (request()->has('size')) {
            $this->validate(request(), [
                'file' => 'max:' . request()->input('size') * 1024,
            ]);
        }
        // If width or height is preset - we are validating it as an image
        if (request()->has('width') || request()->has('height')) {
            $this->validate(request(), [
                'file' => sprintf(
                    'image|dimensions:max_width=%s,max_height=%s',
                    request()->input('width', 100000),
                    request()->input('height', 100000)
                ),
            ]);
        }

        $path = storage_path('tmp/uploads');

        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        } catch (\Exception $e) {
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }


    public function manualStoreMedia($file)
    {

        $path = storage_path('tmp/uploads');

        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        } catch (\Exception $e) {
        }

        if (is_array($file)) {
            $files = $file;
            $response = [];
            foreach ($files as $key => $file) {
                $name = uniqid() . '_' . trim($file->getClientOriginalName());
                $file->move($path, $name);
                $response[$key] = ['name' => $name, 'original_name' => $file->getClientOriginalName()];
            }
            return $response;
        } else {
            $name = uniqid() . '_' . trim($file->getClientOriginalName());

            $file->move($path, $name);

            return array(
                'name' => $name,
                'original_name' => $file->getClientOriginalName()
            );
        }
    }

    public function saveArticlePdf($file, $file_name, $disk = 'articles')
    {
        $path = $file->storeAs(
            now()->format('Y-m'),
            $file_name . '_ResearchAfricaPublications.' . $file->getClientOriginalExtension(),
            $disk
        );
        return $path;
    }

    public function deleteExistingFile($filepath, $disk = 'articles')
    {
        try {
            Storage::disk($disk)->delete($filepath);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}

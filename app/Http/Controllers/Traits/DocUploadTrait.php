<?php

namespace App\Http\Controllers\Traits;

trait DocUploadTrait
{

    public function uploadFile($file)
    {
        $path = 'tmp/uploads';


        try {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        } catch (\Exception $e) {
        }


        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->storeAs($path, $name);

        return array(
            'name' => $name,
            'original_name' => $file->getClientOriginalName()
        );
    }
}

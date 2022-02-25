<?php

namespace App\Traits;

use App\Models\Attachment;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Storage;

trait Uploader
{
    use ApiResponser;


    public function uploadAttachment($photo, $attachableType)
    {

        $loc = Attachment::MORPHS[$attachableType];
        $split_directory = explode('\\', $loc);
        $directory = $split_directory[count($split_directory) - 1];

        $data = $photo;

        return $this->storeFileInStorage($data, $directory);

    }

    public function storeFileInStorage($data, $loc)
    {
        //set a unique name for the stored file
        $sampleName = 'UserUpload';
        $fileName = $sampleName . '-' . date('mdYHis') . uniqid() . '.' . $data->getClientOriginalExtension();
        //store the file in storage and put the path in path variable
        $path = $data->storeAs('public/' . $loc, $fileName);

        return str_replace('public/', 'storage/', $path);
    }


    public function deleteFileFromStorage($path)
    {

        $localPath = str_replace('storage/', 'app/public/', $path);

        if (Storage::exists(str_replace('app/public', 'public', $localPath)))
            unlink(storage_path($localPath));
        else
            return false;

    }
}

<?php


namespace App\Traits;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;

trait Attachments
{
    public function storeAttachment($file, $attachable_id, $attachable_type)
    {

        $path_parts = pathinfo($file->getClientOriginalName());
        $split_directory = explode('\\', $attachable_type);
        $directory = $split_directory[count($split_directory) - 1];

        try {
            $path = Storage::disk('public')->putFileAs(
                $directory, $file, time() . ' ' . $file->getClientOriginalName()
            );
            $attachment = new Attachment();
            $attachment->url = $path;
            $attachment->attachable_type = $attachable_type;
            $attachment->attachable_id = $attachable_id;
            $attachment->title = $path_parts['filename'];
            $attachment->save();
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }
}

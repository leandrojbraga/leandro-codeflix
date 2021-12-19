<?php

namespace App\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadFiles
{   
    protected abstract function uploadDir();

    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }

    /**
     * @param UploadedFile[] $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }


    /**
     * @param string/UploadedFile $files
     */
    public function deleteFile($file)
    {   
        $fileName = $file instanceof UploadedFile ? $file->hashName() : $file;
        Storage::delete("{$this->uploadDir()}/{$fileName}");
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    public static function extractFiles(array &$attributes = [])
    {
        $files = [];
        foreach(self::$fileFields as $file){
            if (isset($attributes[$file]) && $attributes[$file] instanceof UploadedFile) {
                $files[] = $attributes[$file];
                $attributes[$file] = $attributes[$file]->hashName();
            }
        }

        return $files;
    }

    
}

<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadFiles
{   
    public $oldFiles = [];

    protected abstract function uploadDir();

    public static function bootUploadFiles() {
        static::updating(function (Model $model) {
            $fieldsUpdated = array_keys($model->getDirty());

            foreach(self::$fileFields as $field){
                if(in_array($field, $fieldsUpdated)) {
                    $file = $model->getOriginal($field);
                    
                    if ($file) {
                        $model->oldFiles[] = $file;
                    }
                }
            }
        });
    }

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

        if (empty(Storage::files($this->uploadDir()))) {
            Storage::deleteDirectory($this->uploadDir());
        }
    }

    public function deleteOldFiles()
    {
        $this->deleteFiles($this->oldFiles);
    }

    public static function extractFiles(array &$attributes = [])
    {
        $files = [];
        foreach(self::$fileFields as $field){
            if (isset($attributes[$field]) && $attributes[$field] instanceof UploadedFile) {
                $files[] = $attributes[$field];
                $attributes[$field] = $attributes[$field]->hashName();
            }
        }

        return $files;
    }

    
}

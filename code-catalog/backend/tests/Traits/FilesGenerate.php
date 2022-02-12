<?php

namespace Tests\Traits;

use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;

trait FilesGenerate
{   
    private $mimeTypeDefault = [
        'mp4' => 'video/mp4',
        'jpg' => 'image/jpeg',
        'png' => 'image/png'
    ];
    private $sizeDefault = 1;

    public function newFile($name, $extension, $mimeType=null, $size=null) : File
    {
        $fileMime = $mimeType ? $mimeType : $this->mimeTypeDefault[$extension];
        $fileSize = $size ? $size : $this->sizeDefault;

        return UploadedFile::fake()
                            ->create($name.'.'.$extension)
                            ->mimeType($fileMime)
                            ->size($fileSize);
    }

    public function newFiles($name, $extension, $quantity, $mimeType=null, $size=null) : array
    {   
        $files = [];
        for ($i=1; $i <= $quantity; $i++) { 
            array_push(
                $files, 
                $this->newFile($name.$i, $extension, $mimeType, $size)
            );
        }
        return $files;
    }
}
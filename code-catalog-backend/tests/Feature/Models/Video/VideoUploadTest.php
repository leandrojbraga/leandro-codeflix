<?php

namespace Tests\Feature\Models\Video;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoUploadTest extends BaseVideoTest
{   
    public function testUploadFile() {
        Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');

        $model = $this->getModelCreated(
            $this->sendData + ['movie_file' => $file]
        );
        
        Storage::assertExists("{$model->id}/{$file->hashName()}");
    }
}

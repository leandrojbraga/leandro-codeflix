<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Traits\FilesGenerate;

class VideoControllerUploadTest extends BaseVideoControllerTest
{   
    use FilesGenerate;
    
    public function testInvalidationData()
    {   
        // Test create
        $this->setRoute('store');
        $this->assertInvalidationFile('POST');

        // Test update
        $this->setRoute('update', ['video' => $this->getFactoryModel()->id]);
        $this->assertInvalidationFile('PUT');
    }
    
    public function assertInvalidationFile($method) {
        Storage::fake();

        $data = [
            'movie_file' => $this->newFile(
                'video', 'mp4', null, Video::MAX_SIZE_MOVIE_FILE + 1),
            'thumbnail_file' => $this->newFile(
                'image', 'jpg', null, Video::MAX_SIZE_THUMBNAIL_FILE + 1)
        ];
        $attributeRuleReplaces = [
            'movie_file' => [ 'max' => Video::MAX_SIZE_MOVIE_FILE ],
            'thumbnail_file' => [ 'max' => Video::MAX_SIZE_THUMBNAIL_FILE ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'max.file', $attributeRuleReplaces
        );

        $data = [
            'movie_file' => $this->newFile(
                'video', 'mp4', 'other_'. Video::MIME_TYPE_MOVIE_FILE),
            'thumbnail_file' => $this->newFile(
                'image', 'jpg', 'other_'. Video::MIME_TYPE_THUMBNAIL_FILE[0])
        ];

        $attributeRuleReplaces = [
            'movie_file' => [ 'values' => Video::MIME_TYPE_MOVIE_FILE ],
            'thumbnail_file' => [ 'values' => implode(', ', Video::MIME_TYPE_THUMBNAIL_FILE) ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'mimetypes', $attributeRuleReplaces
        );

        $data = [
            'movie_file' => "video.mp4",
            'thumbnail_file' => "image.jpg"
        ];

        $this->assertInvalidationData(
            $method, $data, 'file'
        );
    }
    
    public function testSaveFile() {
        Storage::fake();
        
        $files = [
            'movie_file' => $this->newFile('video', 'mp4'),
            'thumbnail_file' => $this->newFile('image', 'jpg')
        ];
        
        
        $this->setRoute('store');
        $this->assertStore(
            $this->sendData + $this->sendConstrains + $files,
            $this->sendData + [
                'opened' => false,
                'movie_file' => $files['movie_file']->hashName(),
                'thumbnail_file' => $files['thumbnail_file']->hashName(),
                'deleted_at' => null
            ]
        );

        Storage::assertExists("{$this->getRequestId()}/{$files['movie_file']->hashName()}");
        Storage::assertExists("{$this->getRequestId()}/{$files['thumbnail_file']->hashName()}");
    }
}

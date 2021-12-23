<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoControllerUploadTest extends BaseVideoControllerTest
{   
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
            'movie_file' => UploadedFile::fake()
                            ->create('video.mp4')
                            ->size(Video::MAX_SIZE_MOVIE_FILE + 1)
        ];
        $attributeRuleReplaces = [
            'movie_file' => [ 'max' => Video::MAX_SIZE_MOVIE_FILE ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'max.file', $attributeRuleReplaces
        );

        $data = [
            'movie_file' => UploadedFile::fake()
                            ->create('video.mp4')
                            ->mimeType('video/quicktime')
        ];
        $attributeRuleReplaces = [
            'movie_file' => [ 'values' => Video::MIME_TYPE_MOVIE_FILE ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'mimetypes', $attributeRuleReplaces
        );

        $data = [
            'movie_file' => "video.mp4"
        ];

        $this->assertInvalidationData(
            $method, $data, 'file'
        );
    }
    
    public function testSaveFile() {
        UploadedFile::fake()->image('image.jpg');

        Storage::fake();
        
        $file = UploadedFile::fake()->create('video.mp4');
        
        
        $this->setRoute('store');
        $this->assertStore(
            $this->sendData + $this->sendConstrains + ['movie_file' => $file],
            $this->sendData + [
                'opened' => false,
                'movie_file' => $file->hashName(),
                'deleted_at' => null
            ]
        );

        Storage::assertExists("{$this->getRequestId()}/{$file->hashName()}");
    }
}

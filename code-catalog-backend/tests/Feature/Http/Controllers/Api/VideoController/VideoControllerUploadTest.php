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
            'trailer_file' => $this->newFile(
                'trailer', 'mp4', null, Video::MAX_SIZE_TRAILER_FILE + 1),
            'movie_file' => $this->newFile(
                'movie', 'mp4', null, Video::MAX_SIZE_MOVIE_FILE + 1),
            'thumbnail_file' => $this->newFile(
                'thumb', 'jpg', null, Video::MAX_SIZE_THUMBNAIL_FILE + 1),
            'banner_file' => $this->newFile(
                'banner', 'jpg', null, Video::MAX_SIZE_BANNER_FILE + 1)
        ];
        $attributeRuleReplaces = [
            'trailer_file' => [ 'max' => Video::MAX_SIZE_TRAILER_FILE],
            'movie_file' => [ 'max' => Video::MAX_SIZE_MOVIE_FILE ],
            'thumbnail_file' => [ 'max' => Video::MAX_SIZE_THUMBNAIL_FILE ],
            'banner_file' => [ 'max' => Video::MAX_SIZE_BANNER_FILE ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'max.file', $attributeRuleReplaces
        );

        $data = [
            'trailer_file' => $this->newFile(
                'trailer', 'mp4', 'other_'. Video::MIME_TYPE_TRAILER_FILE),
            'movie_file' => $this->newFile(
                'movie', 'mp4', 'other_'. Video::MIME_TYPE_MOVIE_FILE),
            'thumbnail_file' => $this->newFile(
                'thumb', 'jpg', 'other_'. Video::MIME_TYPE_THUMBNAIL_FILE[0]),
            'banner_file' => $this->newFile(
                'banner', 'jpg', 'other_'. Video::MIME_TYPE_BANNER_FILE[0])
        ];

        $attributeRuleReplaces = [
            'trailer_file' => [ 'values' => Video::MIME_TYPE_TRAILER_FILE ],
            'movie_file' => [ 'values' => Video::MIME_TYPE_MOVIE_FILE ],
            'thumbnail_file' => [ 'values' => implode(', ', Video::MIME_TYPE_THUMBNAIL_FILE) ],
            'banner_file' => [ 'values' => implode(', ', Video::MIME_TYPE_BANNER_FILE) ]
        ];

        $this->assertInvalidationData(
            $method, $data, 'mimetypes', $attributeRuleReplaces
        );

        $data = [
            'trailer_file' => "trailer.mp4",
            'movie_file' => "movie.mp4",
            'thumbnail_file' => "thumb.jpg",
            'banner_file' => "banner.jpg"
        ];

        $this->assertInvalidationData(
            $method, $data, 'file'
        );
    }
    
    public function testSaveFile() {
        Storage::fake();
        
        $files = [
            'trailer_file' => $this->newFile('trailer', 'mp4'),
            'movie_file' => $this->newFile('movie', 'mp4'),
            'thumbnail_file' => $this->newFile('thumb', 'jpg'),
            'banner_file' => $this->newFile('banner', 'jpg')
        ];
        
        
        $this->setRoute('store');
        $this->assertStore(
            $this->sendData + $this->sendConstrains + $files,
            $this->sendData + [
                'opened' => false,
                'trailer_file' => $files['trailer_file']->hashName(),
                'movie_file' => $files['movie_file']->hashName(),
                'thumbnail_file' => $files['thumbnail_file']->hashName(),
                'banner_file' => $files['banner_file']->hashName(),
                'deleted_at' => null
            ]
        );

        Storage::assertExists("{$this->getRequestId()}/{$files['trailer_file']->hashName()}");
        Storage::assertExists("{$this->getRequestId()}/{$files['movie_file']->hashName()}");
        Storage::assertExists("{$this->getRequestId()}/{$files['thumbnail_file']->hashName()}");
        Storage::assertExists("{$this->getRequestId()}/{$files['banner_file']->hashName()}");
    }
}

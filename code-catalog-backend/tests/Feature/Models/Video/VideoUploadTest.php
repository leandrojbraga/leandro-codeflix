<?php

namespace Tests\Feature\Models\Video;

use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\Exceptions\TestException;
use Tests\Traits\FilesGenerate;

class VideoUploadTest extends BaseVideoTest
{   
    use FilesGenerate;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    private function createVideo() {
        return $this->getModelCreated(
            $this->sendData + [
                'movie_file' => $this->newFile('video','mp4'),
                'thumbnail_file' => $this->newFile('image','jpg')
            ]
        );
    }

    public function testIfCreateSuccessUploadFile() {
        $video = $this->createVideo();
        
        Storage::assertExists("{$video->id}/{$video->movie_file}");
        Storage::assertExists("{$video->id}/{$video->thumbnail_file}");
    }

    public function testIfCreateExceptionDeleteFile() {
        Event::listen(TransactionCommitted::class, function() {
            throw new TestException();
        });

        $hasError = false;
        try {
            $video = $this->createVideo();
        } catch (TestException $e) {
            $this->assertCount(0, Storage::allFiles());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testIfUpdateSuccessUploadFile() {
        $video = $this->getModelCreated($this->sendData);
        $movieFile = $this->newFile('video','mp4');
        $thumbnailFile = $this->newFile('image','jpg');

        
        $video->update(
            $this->sendData + [
                'movie_file' => $movieFile,
                'thumbnail_file' => $thumbnailFile
            ]
        );
        
        Storage::assertExists("{$video->id}/{$video->movie_file}");
        Storage::assertExists("{$video->id}/{$video->thumbnail_file}");

        $newMovieFile = $this->newFile('video2','mp4');
        $video->update(
            $this->sendData + [ 'movie_file' => $newMovieFile ]
        );

        Storage::assertExists("{$video->id}/{$thumbnailFile->hashName()}");
        Storage::assertExists("{$video->id}/{$newMovieFile->hashName()}");
        Storage::assertMissing("{$video->id}/{$movieFile->hashName()}");
    }

    public function testIfUploadExceptionDeleteFile() {
        $video = $this->createVideo();
        $movieFileName = $video->movie_file;
        $thumbnailFileName = $video->thumbnail_file;
        
        Storage::assertExists("{$video->id}/{$movieFileName}");
        Storage::assertExists("{$video->id}/{$thumbnailFileName}");

        Event::listen(TransactionCommitted::class, function() {
            throw new TestException();
        });

        $newMovieFile = $this->newFile('video2','mp4');

        $hasError = false;
        try {
            $video->update(
                $this->sendData + [ 'movie_file' => $newMovieFile ]
            );
        } catch (TestException $e) {
            Storage::assertExists("{$video->id}/{$movieFileName}");
            Storage::assertExists("{$video->id}/{$thumbnailFileName}");
            Storage::assertMissing("{$video->id}/{$newMovieFile->hashName()}");
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }
}

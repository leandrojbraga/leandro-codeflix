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
                'trailer_file' => $this->newFile('trailer','mp4'),
                'movie_file' => $this->newFile('movie','mp4'),
                'thumbnail_file' => $this->newFile('thumb','jpg'),
                'banner_file' => $this->newFile('banner','jpg')
            ]
        );
    }

    public function testIfCreateSuccessUploadFile() {
        $video = $this->createVideo();
        
        Storage::assertExists("{$video->id}/{$video->trailer_file}");
        Storage::assertExists("{$video->id}/{$video->movie_file}");
        Storage::assertExists("{$video->id}/{$video->thumbnail_file}");
        Storage::assertExists("{$video->id}/{$video->banner_file}");
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
        $trailerFile = $this->newFile('trailer','mp4');
        $movieFile = $this->newFile('movie','mp4');
        $thumbnailFile = $this->newFile('thumb','jpg');
        $bannerFile = $this->newFile('banner','jpg');

        
        $video->update(
            $this->sendData + [
                'trailer_file' => $trailerFile,
                'movie_file' => $movieFile,
                'thumbnail_file' => $thumbnailFile,
                'banner_file' => $bannerFile
            ]
        );
        
        Storage::assertExists("{$video->id}/{$video->trailer_file}");
        Storage::assertExists("{$video->id}/{$video->movie_file}");
        Storage::assertExists("{$video->id}/{$video->thumbnail_file}");
        Storage::assertExists("{$video->id}/{$video->banner_file}");

        $newMovieFile = $this->newFile('movie2','mp4');
        $newBannerFile = $this->newFile('banner2','jpg');
        $video->update(
            $this->sendData + [
                'movie_file' => $newMovieFile,
                'banner_file' => $newBannerFile
            ]
        );

        Storage::assertExists("{$video->id}/{$trailerFile->hashName()}");
        Storage::assertExists("{$video->id}/{$thumbnailFile->hashName()}");

        Storage::assertExists("{$video->id}/{$newMovieFile->hashName()}");
        Storage::assertMissing("{$video->id}/{$movieFile->hashName()}");

        Storage::assertExists("{$video->id}/{$newBannerFile->hashName()}");
        Storage::assertMissing("{$video->id}/{$bannerFile->hashName()}");
    }

    public function testIfUploadExceptionDeleteFile() {
        $video = $this->createVideo();
        $trailerFileName = $video->trailer_file;
        $movieFileName = $video->movie_file;
        $thumbnailFileName = $video->thumbnail_file;
        $bannerFileName = $video->banner_file;
        
        Storage::assertExists("{$video->id}/{$trailerFileName}");
        Storage::assertExists("{$video->id}/{$movieFileName}");
        Storage::assertExists("{$video->id}/{$thumbnailFileName}");
        Storage::assertExists("{$video->id}/{$bannerFileName}");


        Event::listen(TransactionCommitted::class, function() {
            throw new TestException();
        });

        $newMovieFile = $this->newFile('movie2','mp4');
        $newBannerFile = $this->newFile('banner2','jpg');

        $hasError = false;
        try {
            $video->update(
                $this->sendData + [
                    'movie_file' => $newMovieFile,
                    'banner_file' => $newBannerFile
                ]
            );
        } catch (TestException $e) {
            Storage::assertExists("{$video->id}/{$trailerFileName}");
            Storage::assertExists("{$video->id}/{$movieFileName}");
            Storage::assertExists("{$video->id}/{$thumbnailFileName}");
            Storage::assertExists("{$video->id}/{$bannerFileName}");

            Storage::assertMissing("{$video->id}/{$newMovieFile->hashName()}");
            Storage::assertMissing("{$video->id}/{$newBannerFile->hashName()}");
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testVideoFilesUrl() {
        $video = $this->createVideo();
        $pathRoot = '/storage';

        $this->assertEquals(
            $video->trailer_file ? "{$pathRoot}/{$video->id}/{$video->trailer_file}" : null, 
            $video->trailer_file_url
        );

        $this->assertEquals(
            $video->movie_file ? "{$pathRoot}/{$video->id}/{$video->movie_file}" : null, 
            $video->movie_file_url
        );

        $this->assertEquals(
            $video->thumbnail_file ? "{$pathRoot}/{$video->id}/{$video->thumbnail_file}" : null, 
            $video->thumbnail_file_url
        );

        $this->assertEquals(
            $video->banner_file ? "{$pathRoot}/{$video->id}/{$video->banner_file}" : null, 
            $video->banner_file_url
        );
    }
}

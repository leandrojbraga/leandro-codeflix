<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Support\Facades\Config;
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

    protected function assertFileExistsInStorage($model, array $files_name) {
        foreach ($files_name as $file_name) {
            Storage::assertExists(
                $model->relativeFilePath($file_name)
            );
        }
    }

    protected function assertFileMissingInStorage($model, array $files_name) {
        foreach ($files_name as $file_name) {
            Storage::assertMissing(
                $model->relativeFilePath($file_name)
            );
        }
    }

    public function testIfCreateSuccessUploadFile() {
        $video = $this->createVideo();
        
        $this->assertFileExistsInStorage(
            $video,
            [
                $video->trailer_file, $video->movie_file,
                $video->thumbnail_file, $video->banner_file
            ]
        );
    }

    public function testIfCreateExceptionDeleteFile() {
        Storage::fake();
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
        
        $this->assertFileExistsInStorage(
            $video,
            [
                $video->trailer_file, $video->movie_file,
                $video->thumbnail_file, $video->banner_file
            ]
        );

        $newMovieFile = $this->newFile('movie2','mp4');
        $newBannerFile = $this->newFile('banner2','jpg');
        $video->update(
            $this->sendData + [
                'movie_file' => $newMovieFile,
                'banner_file' => $newBannerFile
            ]
        );

        $this->assertFileExistsInStorage(
            $video,
            [
                $trailerFile->hashName(), $thumbnailFile->hashName(),
                $newMovieFile->hashName(), $newBannerFile->hashName()
            ]
        );

        $this->assertFileMissingInStorage(
            $video,
            [
                $movieFile->hashName(),
                $bannerFile->hashName()
            ]
        );
    }

    public function testIfUploadExceptionDeleteFile() {
        $video = $this->createVideo();
        $trailerFileName = $video->trailer_file;
        $movieFileName = $video->movie_file;
        $thumbnailFileName = $video->thumbnail_file;
        $bannerFileName = $video->banner_file;
        
        $this->assertFileExistsInStorage(
            $video,
            [
                $trailerFileName, $movieFileName,
                $thumbnailFileName, $bannerFileName
            ]
        );

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
            $this->assertFileExistsInStorage(
                $video,
                [
                    $trailerFileName, $movieFileName,
                    $thumbnailFileName, $bannerFileName
                ]
            );

            $this->assertFileMissingInStorage(
                $video,
                [
                    $newMovieFile->hashName(),
                    $newBannerFile->hashName()
                ]
            );

            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    protected function assertFilesUrl($model, $baseUrl, $files_name) {
        foreach ($files_name as $file_name) {
            $fileUrl = $model->{$file_name} ? "{$baseUrl}/{$model->relativeFilePath($model->{$file_name})}" : null;

            $this->assertEquals(
                $fileUrl,
                $model->{$file_name."_url"}
            );
        }
    }

    public function testVideoFilesUrl() {
        $video = $this->createVideo();

        $localDriver = config('filesystems.default');
        $baseUrl = config('filesystems.disks.'.$localDriver)['url'];
        $this->assertFilesUrl($video, $baseUrl, Video::$fileFields);

        Config::set('filesystems.default', 'gcs');
        $baseUrl = config('filesystems.disks.gcs.storage_api_uri');
        $this->assertFilesUrl($video, $baseUrl, Video::$fileFields);
    }
}

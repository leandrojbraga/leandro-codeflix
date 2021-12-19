<?php

namespace Tests\Unit\Models\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Stubs\Models\Traits\UploadFilesStub;

class UploadFilesUnitTest extends TestCase
{
    private $uploadFile;

    protected function setUp(): void
    {   
        parent::setUp();
        $this->uploadFile = new UploadFilesStub();
    }

    private function getUploadedFile() {
        $file = UploadedFile::fake()->create('video.mp4');
        $this->uploadFile->uploadFile($file);

        return $file;
    }

    private function getUploadedFiles() {
        $files = [];
        for ($i=1; $i < 3; $i++) { 
            array_push($files, UploadedFile::fake()->create("video{$i}.mp4"));
        }
        $this->uploadFile->uploadFiles($files);

        return $files;
    }

    public function testFileUpload()
    {   
        
        $file = $this->getUploadedFile();
        Storage::assertExists("{$this->uploadFile->videosDir}/{$file->hashName()}");
    }

    public function testFilesUpload()
    {   
        Storage::fake();
        
        $files = $this->getUploadedFiles();
        foreach ($files as $file) {
            Storage::assertExists("{$this->uploadFile->videosDir}/{$file->hashName()}");
        }
        
    }

    public function testFileDelete()
    {   
        Storage::fake();
        
        $file = $this->getUploadedFile();
        $fileName = $file->hashName();
        $this->uploadFile->deleteFile($fileName);
        Storage::assertMissing("{$this->uploadFile->videosDir}/{$fileName}");

        $file = $this->getUploadedFile();
        $this->uploadFile->deleteFile($file);
        Storage::assertMissing("{$this->uploadFile->videosDir}/{$file->hashName()}");
        
    }

    public function testFilesDelete()
    {   
        Storage::fake();

        $files = $this->getUploadedFiles();
        
        $deleteFiles = [];
        foreach ($files as $file) {
            if ($file == reset($files)) {
                array_push($deleteFiles, $file->hashName());
            } else {
                array_push($deleteFiles, $file);
            }
            
        }
        $this->uploadFile->deleteFiles($deleteFiles);
        
        foreach ($files as $file) {
            Storage::assertMissing("{$this->uploadFile->videosDir}/{$file->hashName()}");
        }
    }

    public function testExtractFiles() {
        $attributes = [];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(0, $attributes);
        $this->assertCount(0, $files);

        $attributes = ['file1' => 'test'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(1, $attributes);
        $this->assertEquals(['file1' => 'test'], $attributes);
        $this->assertCount(0, $files);

        $attributes = ['file1' => 'test', 'file2' => 'test2'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => 'test', 'file2' => 'test2'], $attributes);
        $this->assertCount(0, $files);

        $file1 = UploadedFile::fake()->create('video.mp4');
        $file2 = UploadedFile::fake()->create('image.png');

        $attributes = ['file1' => $file1, 'test' => 'test'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => $file1->hashName(), 'test' => 'test'], $attributes);
        $this->assertEquals([$file1], $files);

        
        $attributes = ['file1' => $file1, 'file2' => $file2, 'test' => 'test'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(3, $attributes);
        $this->assertEquals(
            ['file1' => $file1->hashName(), 'file2' => $file2->hashName(), 'test' => 'test'], 
            $attributes
        );
        $this->assertEquals([$file1, $file2], $files);
    }
}

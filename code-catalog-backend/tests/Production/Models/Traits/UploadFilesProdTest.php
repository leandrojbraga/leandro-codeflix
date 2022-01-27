<?php

namespace Tests\Production\Models\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Stubs\Models\Traits\UploadFilesStub;
use Tests\Traits\FilesGenerate;
use Tests\Traits\TestProduction;
use Tests\Traits\TestStorages;

class UploadFilesProdTest extends TestCase
{
    use FilesGenerate, TestStorages, TestProduction;

    private $uploadFile;

    protected function setUp(): void
    {   
        parent::setUp();
        $this->skipTestIfNotProd("Testing Production");
        $this->uploadFile = new UploadFilesStub();
        Config::set('filesystems.default', 'gcs');
        $this->deleteAllFiles();
        
    }

    protected function tearDown(): void
    {
        $this->deleteAllFiles();
    }

    private function getUploadedFile() {
        $file = $this->newFile('video', 'mp4');
        $this->uploadFile->uploadFile($file);

        return $file;
    }

    private function getUploadedFiles() {
        $files = $this->newFiles('video', 'mp4', 2);
        $this->uploadFile->uploadFiles($files);

        return $files;
    }

    public function testFileUpload()
    {   
        $file = $this->getUploadedFile();
        Storage::assertExists("{$this->uploadFile->videosDir}/{$file->hashName()}");
    }

    public function testFileUploadUrl()
    {   
        $file = $this->getUploadedFile();
        $fileUrl = $this->uploadFile->getFileUrl($file->hashName());
        
        $apiRootUrl = env('GOOGLE_CLOUD_STORAGE_API_URI');
        
        $this->assertEquals(
            $fileUrl,
            "{$apiRootUrl}/{$this->uploadFile->videosDir}/{$file->hashName()}"
        );        
    }

    public function testFilesUpload()
    {   
        $files = $this->getUploadedFiles();
        foreach ($files as $file) {
            Storage::assertExists("{$this->uploadFile->videosDir}/{$file->hashName()}");
        }
        
    }

    public function testFileDelete()
    {   
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

        $file1 = $this->newFile('video', 'mp4');
        $file2 = $this->newFile('image', 'png');

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

    public function testDeleteOldFiles() {
        $files = $this->getUploadedFiles();
        $this->uploadFile->deleteOldFiles();
        $this->assertCount(2, Storage::allFiles());
        
        $this->uploadFile->oldFiles = [$files[0]->hashName()];
        $this->uploadFile->deleteOldFiles();
        Storage::assertMissing("{$this->uploadFile->videosDir}/{$files[0]->hashName()}");
        Storage::assertExists("{$this->uploadFile->videosDir}/{$files[1]->hashName()}");
        
    }
}

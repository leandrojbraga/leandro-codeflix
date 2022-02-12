<?php

namespace Tests\Feature\Models\Traits;

use Tests\Stubs\Models\Traits\UploadFilesStub;
use Tests\TestCase;


class UploadFilesTest extends TestCase
{
   private $uploadFile;

   protected function setUp(): void
   {
        parent::setUp();
        UploadFilesStub::dropTable();
        UploadFilesStub::createTable();
        $this->uploadFile = new UploadFilesStub();
   }
   
    protected function tearDown(): void
    {
        UploadFilesStub::dropTable();
        parent::tearDown();
    }

    public function testMakeOldFieldsOnSaving() {
        $this->uploadFile->fill([
            'name' => 'file test',
            'file1' => 'file1.mp4',
            'file2' => 'file2.png'
        ]);
        $this->uploadFile->save();

        $this->assertCount(0, $this->uploadFile->oldFiles);

        $this->uploadFile->update([
            'name' => 'update test',
            'file2' => 'file3.jpg'
        ]);

        $this->assertEqualsCanonicalizing(
            ['file2.png'], $this->uploadFile->oldFiles
        );
    }

    public function testNotMakeOldFieldsOnSavingIfOriginalIsNull() {
        $uploadFile = UploadFilesStub::create([
            'name' => 'new test',
        ]);

        $this->assertCount(0, $uploadFile->oldFiles);

        $uploadFile->update([
            'name' => 'update test',
            'file2' => 'file.jpg'
        ]);
        
        $this->assertEqualsCanonicalizing(
            [], $uploadFile->oldFiles
        );

        $uploadFile->update([
            'name' => 'update 2',
            'file1' => 'file1.mp4',
            'file2' => 'file2.png'
        ]);
        
        $this->assertEqualsCanonicalizing(
            ['file.jpg'], $uploadFile->oldFiles
        );
        
    }

}

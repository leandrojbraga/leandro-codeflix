<?php

namespace Tests\Stubs\Models\Traits;

use App\Models\Traits\UploadFiles;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

class UploadFilesStub extends Model
{   
    use UploadFiles;
    
    public $videosDir = "1";

    // protected static $tableName = 'upload_file_stubs';
    // protected $table = 'upload_file_stubs';
    // protected $fillable = [
    //     'name', 'file1', 'file2'
    // ];
    protected static $fileFields = ['file1', 'file2'];
    
    // public static function createTable() {
    //     Schema::create(self::$tableName, function (Blueprint $table) {
    //         $table->bigIncrements('id');
    //         $table->string('name');
    //         $table->string('file1');
    //         $table->string('file2');
    //         $table->timestamps();
    //     });
    // }

    // public static function dropTable() {
    //     Schema::dropIfExists(self::$tableName);
    // }

    protected function uploadDir()
    {
        return $this->videosDir;
    }

}


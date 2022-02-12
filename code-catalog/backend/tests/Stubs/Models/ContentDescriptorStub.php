<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ContentDescriptorStub extends Model
{   
    protected static $tableName = 'content_descriptor_stubs';
    protected $table = 'content_descriptor_stubs';
    protected $fillable = ['name'];
    
    public static function createTable() {
        Schema::create(self::$tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    public static function dropTable() {
        Schema::dropIfExists(self::$tableName);
    }

}


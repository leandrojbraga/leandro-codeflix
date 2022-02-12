<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CategoryStub extends Model
{   
    protected static $tableName = 'category_stubs';
    protected $table = 'category_stubs';
    protected $fillable = ['name', 'description'];
    
    public static function createTable() {
        Schema::create(self::$tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public static function dropTable() {
        Schema::dropIfExists(self::$tableName);
    }

}


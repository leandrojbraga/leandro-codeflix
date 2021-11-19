<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GenreStub extends Model
{   
    protected static $tableName = 'genre_stubs';
    protected $table = 'genre_stubs';
    protected $fillable = ['name', 'is_active'];
    
    public static function createTable() {
        Schema::create(self::$tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public static function dropTable() {
        Schema::dropIfExists(self::$tableName);
    }

}


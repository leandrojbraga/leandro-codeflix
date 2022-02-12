<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class VideoStub extends Model
{   
    const RATING_GENERAL_AUDIENCE = 'L';

    protected static $tableName = 'video_stubs';
    protected $table = 'video_stubs';
    protected $fillable = [
        'title', 'year_launched', 'opened', 'rating'
    ];
    
    public static function createTable() {
        Schema::create(self::$tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->smallInteger('year_launched');
            $table->boolean('opened')->default(false);
            $table->string('rating', 2);
            $table->timestamps();
        });
    }

    public static function dropTable() {
        Schema::dropIfExists(self::$tableName);
    }

}


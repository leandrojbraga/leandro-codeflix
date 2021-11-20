<?php

namespace Tests\Stubs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CastMemberStub extends Model
{   
    protected static $tableName = 'cast_member_stubs';
    protected $table = 'cast_member_stubs';
    protected $fillable = ['name', 'type'];
    
    public static function createTable() {
        Schema::create(self::$tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->smallInteger('type');
            $table->timestamps();
        });
    }

    public static function dropTable() {
        Schema::dropIfExists(self::$tableName);
    }

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentDescriptorVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_descriptor_video', function (Blueprint $table) {
            $table->uuid('content_descriptor_id')->index();
            $table->foreign('content_descriptor_id')->references('id')->on('content_descriptors');
            $table->uuid('video_id')->index();
            $table->foreign('video_id')->references('id')->on('videos');
            $table->unique(['content_descriptor_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_descriptor_video');
    }
}

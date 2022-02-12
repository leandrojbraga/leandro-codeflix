<?php

namespace Tests\Feature\Models\Video;


use App\Models\Video;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

abstract class BaseVideoTest extends TestCase
{   
    use DatabaseMigrations, FeatureModelsValidations;

    protected $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sendData = [
            'title' => 'Video title',
            'description' => 'Video description',
            'year_launched' => 2020,
            'rating' => Video::RATINGS[0],
            'duration' => 90
        ];
    }

    protected function model() {
       return Video::class;
    }
}

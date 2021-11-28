<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

class VideoTest extends TestCase
{   
    use DatabaseMigrations, FeatureModelsValidations;

    private $sendData;

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

    public function testList()
    {   
        $this->assertList();
    }

    public function testAllAttributes()
    {   
        $this->assertAttributes(
            [
                'id', 'title', 'description', 'year_launched',
                'opened', 'rating', 'duration',
                'created_at', 'updated_at', 'deleted_at'
            ]
        );
    }

    public function testSave()
    {
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $this->sendData + ['opened' => false]
            ],
            [
                'send_data' =>  $this->sendData + ['opened' => false],
                'test_data' =>  $this->sendData + ['opened' => false]
            ],
            [
                'send_data' =>  $this->sendData + ['opened' => true],
                'test_data' =>  $this->sendData + ['opened' => true]
            ],
            [
                'send_data' =>  array_replace(
                                    $this->sendData,
                                    ['rating' => Video::RATINGS[3]]
                                ),
                'test_data' =>  array_replace(
                                    $this->sendData,
                                    ['rating' => Video::RATINGS[3],
                                        'opened' => false]
                                )
            ]
        ];

        foreach($data as $key => $value) {
            $this->assertCreate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $update_data = array_replace(
                $value['send_data'],
                ['title' => 'Updating title']
            );
            $this->assertEdit(
                $update_data,
                $update_data + ['deleted_at' => null]
            );

            
            $model = $this->model()::all()->first();
            $model->delete();
        }
    }

    public function testUuid4()
    {
        $this->assertIdIsUuid4(
            $this->getModelCreated($this->sendData)->id
        );
    }

    public function testSoftDelete() {
        $modelCreated = $this->getModelCreated(
            $this->sendData + ['is_active' => true]
        );

        $this->assertSoftDelete($modelCreated->id);
    }
}

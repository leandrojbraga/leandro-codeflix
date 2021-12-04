<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

class CategoryTest extends TestCase
{
    use DatabaseMigrations, FeatureModelsValidations;

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sendData = [ 'name' => 'Category Test' ];
    }

    protected function model() {
       return Category::class;
    }

    public function testList()
    {   
        $this->assertList();
    }

    public function testAllAttributes()
    {   
        $this->assertAttributes(
            [
                'id', 'name', 'description', 'is_active',
                'created_at', 'updated_at', 'deleted_at'
            ]
        );
    }

    public function testSave()
    {
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $this->sendData + ['description' => null,
                                                'is_active' => true]
            ],
            [
                'send_data' => $this->sendData + ['description' => null],
                'test_data' => $this->sendData + ['description' => null]
            ],
            [
                'send_data' => $this->sendData + ['description' => 'test description'],
                'test_data' => $this->sendData + ['description' => 'test description']
            ],
            [
                'send_data' => $this->sendData + ['is_active' => false],
                'test_data' => $this->sendData + ['is_active' => false]
            ],
            [
                'send_data' => $this->sendData + ['is_active' => true],
                'test_data' => $this->sendData + ['is_active' => true]
            ]
        ];

        foreach($data as $key => $value) {
            $this->assertCreate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );

            $update_data = array_replace(
                $value['send_data'],
                ['name' => 'Updating name']
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
            $this->sendData + [
                'description' => 'Category description',
                'is_active' => true
            ]
        );

        $this->assertSoftDelete($modelCreated->id);
    }
}

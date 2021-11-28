<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

class GenreTest extends TestCase
{   
    use DatabaseMigrations, FeatureModelsValidations;

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sendData = [ 'name' => 'Genre Test' ];
    }

    protected function model() {
       return Genre::class;
    }

    public function testList()
    {   
        $this->assertList();
    }

    public function testAllAttributes()
    {   
        $this->assertAttributes(
            [
                'id', 'name', 'is_active',
                'created_at', 'updated_at', 'deleted_at'
            ]
        );
    }

    public function testSave()
    {
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $this->sendData + ['is_active' => true]
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

            $this->model()::truncate();
        }
    }

    public function testSoftDelete() {
        $modelCreated = $this->getModelCreated(
            $this->sendData + ['is_active' => true]
        );

        $this->assertSoftDelete($modelCreated->id);
    }
}

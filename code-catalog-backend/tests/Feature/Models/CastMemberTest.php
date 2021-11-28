<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

class CastMemberTest extends TestCase
{
    use DatabaseMigrations, FeatureModelsValidations;

    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sendData = [ 
            'name' => 'CastMember Test',
            'type' => CastMember::TYPE_DIRECTOR
        ];
    }

    protected function model() {
       return CastMember::class;
    }

    public function testList()
    {   
        $this->assertList();
    }

    public function testAllAttributes()
    {   
        $this->assertAttributes(
            [
                'id', 'name', 'type',
                'created_at', 'updated_at', 'deleted_at'
            ]
        );
    }

    public function testSave()
    {
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $this->sendData
            ],
            [
                'send_data' => array_replace(
                                    $this->sendData,
                                    ['type' => CastMember::TYPE_ACTOR]
                                ),
                'test_data' => array_replace(
                                    $this->sendData,
                                    ['type' => CastMember::TYPE_ACTOR]
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
        $modelCreated = $this->getModelCreated($this->sendData);

        $this->assertSoftDelete($modelCreated->id);
    }
}

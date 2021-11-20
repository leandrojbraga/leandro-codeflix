<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

class CastMemberTest extends TestCase
{
    use DatabaseMigrations, FeatureModelsValidations;

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

    public function testCreate() {
        $name = 'CastMember Test';
        $type = 1;
                
        // Validate a default create
        $data = [ 
            'name' => $name,
            'type' => $type
        ];
        $this->assertCreate(
            $data,
            $data
        );

        //Validate id is Uuid4
        $this->assertIdIsUuid4(
            $this->getModelCreated($data)->id
        );
    }
    
    public function testEdit() {
        $this->assertEdit(
            [
                'name' => 'CastMember old',
                'type' => 1
            ],
            [
                'name' => 'CastMember edited',
                'type' => 2
            ]
        );
    }

    public function testSoftDelete() {
        $modelCreated = $this->getModelCreated([
            'name' => 'CastMember',
            'type' => 1
        ]);

        $this->assertSoftDelete($modelCreated->id);
    }
}

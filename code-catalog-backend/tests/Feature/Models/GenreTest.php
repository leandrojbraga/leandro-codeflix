<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

class GenreTest extends TestCase
{   
    use DatabaseMigrations, FeatureModelsValidations;

    protected function getModel() {
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

    public function testCreate() {
        $name = 'Category Test';
                
        // Validate a default create
        $data = [ 'name' => $name ];
        $this->assertCreate(
            $data,
            $data + [
                'is_active' => true
            ],
            true
        );

        //Validate id is Uuid4
        $this->assertIdIsUuid4(
            $this->getModelCreated($data)->id
        );

        // Validate is_active false
        $data = [
            'name' => $name,
            'is_active' => false
        ];
        $this->assertCreate($data, $data);

        // Validate is_active true
        $data = [
            'name' => $name,
            'is_active' => true
        ];
        $this->assertCreate($data, $data);
    }
    
    public function testEdit() {
        $this->assertEdit(
            [
                'name' => 'Category old',
                'is_active' => false
            ],
            [
                'name' => 'Category edited',
                'is_active' => true
            ]
        );
    }

    public function testSoftDelete() {
        $modelCreated = $this->getModelCreated([
            'name' => 'Category',
            'is_active' => false
        ]);

        $this->assertSoftDelete($modelCreated->id);
    }
}

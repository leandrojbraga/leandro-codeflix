<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\FeatureModelsValidations;

class CategoryTest extends TestCase
{
    use DatabaseMigrations, FeatureModelsValidations;

    protected function getModel() {
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

    public function testCreate() {
        $name = 'Category Test';
        $description = 'Category test description';
                
        // Validate a default create
        $data = [ 'name' => $name ];
        $this->assertCreate(
            $data,
            $data + [
                'description' => null,
                'is_active' => true
            ],
            true
        );

        //Validate id is Uuid4
        $this->assertIdIsUuid4(
            $this->getModelCreated($data)->id
        );

        // Validate description null
        $data = [
            'name' => $name,
            'description' => null,
        ];
        $this->assertCreate($data, $data);

        // Validate description NOT null
        $data = [
            'name' => $name,
            'description' => $description
        ];
        $this->assertCreate($data, $data);

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
                'description' => 'Category old description',
                'is_active' => false
            ],
            [
                'name' => 'Category edited',
                'description' => 'Category edited description',
                'is_active' => true
            ]
        );
    }

    public function testSoftDelete() {
        $modelCreated = $this->getModelCreated([
            'name' => 'Category',
            'description' => 'Category description',
            'is_active' => false
        ]);

        $this->assertSoftDelete($modelCreated->id);
    }
}

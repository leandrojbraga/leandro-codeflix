<?php

namespace Tests\Feature\Models;

use App\Models\Category;

class CategoryTest extends TestModel
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->table = Category::class;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {   
        $this->validateList();
    }

    public function testAllAttributes()
    {   
        $this->validateAllAttributes(
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
        $this->validateCreate(
            [
                'name' => $name
            ],
            [   
                'name' => $name,
                'description' => null,
                'is_active' => true
            ],
            true
        );

        // Validate description null
        $this->validateCreate(
            [
                'name' => $name,
                'description' => null,
            ],
            [   
                'description' => null
            ]
        );

        // Validate description NOT null
        $this->validateCreate(
            [
                'name' => $name,
                'description' => $description
            ],
            [   
                'description' => $description
            ]
        );

        // Validate is_active false
        $this->validateCreate(
            [
                'name' => $name,
                'is_active' => false
            ],
            [   
                'is_active' => false
            ]
        );

        // Validate is_active true
        $this->validateCreate(
            [
                'name' => $name,
                'is_active' => true
            ],
            [   
                'is_active' => true
            ]
        );
    }
    
    public function testEdit() {
        $this->validateEdit(
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
        $this->validateSoftDelete(
            [
                'name' => 'Category',
                'description' => 'Category description',
                'is_active' => false
            ]
        );
    }
}

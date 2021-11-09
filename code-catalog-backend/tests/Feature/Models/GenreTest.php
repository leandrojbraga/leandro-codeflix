<?php

namespace Tests\Feature\Models;

use App\Models\Genre;

class GenreTest extends TestModel
{   
    protected function setUp(): void
    {
        parent::setUp();
        $this->table = Genre::class;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testList()
    {   
        $this->validateList();
    }

    public function testAllAttributes()
    {   
        $this->validateAllAttributes(
            [
                'id', 'name', 'is_active',
                'created_at', 'updated_at', 'deleted_at'
            ]
        );
    }

    public function testCreate() {
        $name = 'Genre Test';
        
        // Validate a default create
        $this->validateCreate(
            [
                'name' => $name
            ],
            [
                'name' => $name,
                'is_active' => true
            ],
            true
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
                'name' => 'Genre old',
                'is_active' => false
            ],
            [
                'name' => 'Genre edited',
                'is_active' => true
            ]
        );
    }
    
    public function testSoftDelete() {
        $this->validateSoftDelete(
            [
                'name' => 'Genre',
                'is_active' => false
            ]
        );
    }
}

<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;


class GenreTest extends TestModel
{
   
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new Genre();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testFillableAttributes() {        
        $this->validateFillableAttributes(
            ['name', 'is_active']
        );
    }

    public function testDatesAttributes() {   
        $this->validateDatesAttributes(
            ['deleted_at', 'created_at', 'updated_at']
        );
    }

    public function testTypeKey() {
        $this->validateTypeKey(
            'string'
        );
    }

    public function testIncrementing() {
        $this->validateIncrementingFalse();
    }

    public function testCastsAttributes() {
        $this->validateCastsAttributes(
            ['is_active']
        );
    }

    public function testUseAllTraits() {
        $this->validateUseAllTraits(
            [ SoftDeletes::class, Uuid::class]
        );
    }
}

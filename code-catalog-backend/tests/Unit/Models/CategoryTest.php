<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;


class CategoryTest extends TestModel
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new Category();
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
            ['name', 'description', 'is_active']
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

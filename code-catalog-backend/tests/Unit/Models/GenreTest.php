<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use Tests\Traits\UnitModelsValidations;

class GenreTest extends TestCase
{
    use UnitModelsValidations;

    protected function getModel() {
        return (new Genre());
    }

    public function testFillableAttributes() {        
        $this->assertFillableAttributes(
            ['name', 'is_active']
        );
    }

    public function testDatesAttributes() {   
        $this->assertDatesAttributes(
            ['deleted_at', 'created_at', 'updated_at']
        );
    }

    public function testTypeKey() {
        $this->assertTypeKey('string');
    }

    public function testIncrementing() {
        $this->assertIncrementingFalse();
    }

    public function testCastsAttributes() {
        $this->assertCastsAttributes(
            ['is_active']
        );
    }

    public function testUseAllTraits() {
        $this->assertUseAllTraits(
            [ SoftDeletes::class, Uuid::class]
        );
    }
}

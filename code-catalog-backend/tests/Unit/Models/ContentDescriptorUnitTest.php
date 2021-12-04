<?php

namespace Tests\Unit\Models;

use App\Models\ContentDescriptor;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use Tests\Traits\UnitModelsValidations;

class ContentDescriptorUnitTest extends TestCase
{
    use UnitModelsValidations;

    protected function model() {
        return (new ContentDescriptor());
    }

    public function testFillableAttributes() {        
        $this->assertFillableAttributes(
            ['name']
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

    public function testUseAllTraits() {
        $this->assertUseAllTraits(
            [ SoftDeletes::class, Uuid::class]
        );
    }
}

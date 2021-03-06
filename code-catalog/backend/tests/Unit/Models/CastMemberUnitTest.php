<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\TestCase;
use Tests\Traits\UnitModelsValidations;

class CastMemberUnitTest extends TestCase
{
    use UnitModelsValidations;

    protected function model() {
        return (new CastMember());
    }

    public function testFillableAttributes() {        
        $this->assertFillableAttributes(
            ['name', 'type']
        );
    }

    public function testDatesAttributes() {   
        $this->assertDatesAttributes(
            ['deleted_at', 'created_at', 'updated_at']
        );
    }

    public function testTypeKey() {
        $this->assertTypeKey(
            'string'
        );
    }

    public function testIncrementing() {
        $this->assertIncrementingFalse();
    }

    public function testCastsAttributes() {
        $this->assertCastsAttributes(
            ['id','type']
        );
    }

    public function testUseAllTraits() {
        $this->assertUseAllTraits(
            [ SoftDeletes::class, Uuid::class ]
        );
    }
}

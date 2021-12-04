<?php

namespace Tests\Unit\Models;

use App\Models\Video;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;
use Tests\Traits\UnitModelsValidations;

class VideoUnitTest extends TestCase
{
    use UnitModelsValidations;

    protected function model() {
        return (new Video());
    }

    public function testFillableAttributes() {        
        $this->assertFillableAttributes(
            ['title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration'
        ]);
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
            ['opened', 'year_launched', 'duration']
        );
    }

    public function testUseAllTraits() {
        $this->assertUseAllTraits(
            [ SoftDeletes::class, Uuid::class ]
        );
    }
}

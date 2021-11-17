<?php

namespace Tests\Traits;

trait UnitModelsValidations
{   
    public function assertFillableAttributes(array $fillable) {
        $this->assertEquals($fillable, $this->getModel()->getFillable());
    }

    public function assertDatesAttributes(array $dates) {   
        $genreDates = $this->getModel()->getDates();
        
        $this->assertEqualsCanonicalizing($dates, $genreDates);
    }

    public function assertTypeKey(string $keyType) {
        $this->assertEquals($keyType, $this->getModel()->getKeyType());
    }

    public function assertIncrementingFalse() {
        $this->assertFalse($this->getModel()->incrementing);
    }

    public function assertIncrementingTrue() {
        $this->assertTrue($this->getModel()->incrementing);
    }

    public function assertCastsAttributes(array $casts) {
        $modelCasts = array_keys($this->getModel()->getCasts());
        
        $this->assertEqualsCanonicalizing($casts, $modelCasts);
    }

    public function assertUseAllTraits(array  $traits) {
        $modelTraits = array_keys(class_uses($this->getModel()));
        
        $this->assertEquals($traits, $modelTraits);
    }
}
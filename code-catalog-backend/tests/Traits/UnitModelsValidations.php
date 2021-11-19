<?php

namespace Tests\Traits;

trait UnitModelsValidations
{   
    protected abstract function model();

    public function assertFillableAttributes(array $fillable) {
        $this->assertEquals($fillable, $this->model()->getFillable());
    }

    public function assertDatesAttributes(array $dates) {   
        $genreDates = $this->model()->getDates();
        
        $this->assertEqualsCanonicalizing($dates, $genreDates);
    }

    public function assertTypeKey(string $keyType) {
        $this->assertEquals($keyType, $this->model()->getKeyType());
    }

    public function assertIncrementingFalse() {
        $this->assertFalse($this->model()->incrementing);
    }

    public function assertIncrementingTrue() {
        $this->assertTrue($this->model()->incrementing);
    }

    public function assertCastsAttributes(array $casts) {
        $modelCasts = array_keys($this->model()->getCasts());
        
        $this->assertEqualsCanonicalizing($casts, $modelCasts);
    }

    public function assertUseAllTraits(array  $traits) {
        $modelTraits = array_keys(class_uses($this->model()));
        
        $this->assertEquals($traits, $modelTraits);
    }
}
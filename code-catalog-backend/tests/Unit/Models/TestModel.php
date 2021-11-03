<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

class TestModel extends TestCase
{   
    protected $model;

    
    
    protected function validateFillableAttributes($fillable) {
        $this->assertEquals($fillable, $this->model->getFillable());
    }

    public function validateDatesAttributes($dates) {   
        $genreDates = $this->model->getDates();
        
        $this->assertEqualsCanonicalizing($dates, $genreDates);
    }

    public function validateTypeKey($keyType) {
        $this->assertEquals($keyType, $this->model->getKeyType());
    }

    public function validateIncrementingFalse() {
        $this->assertFalse($this->model->incrementing);
    }

    public function validateIncrementingTrue() {
        $this->assertTrue($this->model->incrementing);
    }

    public function validateCastsAttributes($casts) {
        $modelCasts = array_keys($this->model->getCasts());
        
        $this->assertEqualsCanonicalizing($casts, $modelCasts);
    }

    public function validateUseAllTraits($traits) {
        $modelTraits = array_keys(class_uses($this->model));
        
        $this->assertEquals($traits, $modelTraits);
    }
}
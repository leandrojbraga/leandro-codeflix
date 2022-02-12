<?php

namespace Tests\Traits;

trait TestProduction
{   
    protected function skipTestIfNotProd($message = '') {
        if (!$this->isTestingProduction()) {
            $this->markTestSkipped($message);
        }
    }

    protected function isTestingProduction() {
        return env('TESTING_PRODUCTION') == true;
    }
}
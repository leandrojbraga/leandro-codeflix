<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\ContentDescriptorStub;
use Tests\Stubs\Resources\ContentDescriptorResourceStub;

class ContentDescriptorControllerStub extends BasicCrudController
{
    protected function model() {
        return ContentDescriptorStub::class;
    }

    protected function validationRules($request) {
        return [
            'name' => 'required|max:100'
        ];
    }
    
    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return ContentDescriptorResourceStub::class;
    }
}
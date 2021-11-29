<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\ContentDescriptorStub;

class ContentDescriptorControllerStub extends BasicCrudController
{
    protected function model() {
        return ContentDescriptorStub::class;
    }

    protected function validationRules() {
        return [
            'name' => 'required|max:100'
        ];
    }


}
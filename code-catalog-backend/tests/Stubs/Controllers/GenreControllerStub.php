<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\GenreStub;

class GenreControllerStub extends BasicCrudController
{
    protected function model() {
        return GenreStub::class;
    }

    protected function simpleValidationRules() {
        return [
            'name' => 'required|max:255',
            'is_active' => 'boolean'
        ];
    }


}
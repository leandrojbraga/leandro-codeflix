<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\GenreStub;
use Tests\Stubs\Resources\GenreResourceStub;

class GenreControllerStub extends BasicCrudController
{
    protected function model() {
        return GenreStub::class;
    }

    protected function validationRules($request) {
        return [
            'name' => 'required|max:255',
            'is_active' => 'boolean'
        ];
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return GenreResourceStub::class;
    }

}
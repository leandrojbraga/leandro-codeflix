<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\CategoryStub;
use Tests\Stubs\Resources\CategoryResourceStub;

class CategoryControllerStub extends BasicCrudController
{   
    protected $paginationSize = 16;

    protected function model() {
        return CategoryStub::class;
    }

    protected function validationRules($request) {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return CategoryResourceStub::class;
    }

}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends BasicCrudController
{   
    protected $paginationSize = 16;

    protected function model() {
        return Category::class;
    }

    protected function validationRules($request) {
        return [
            'name' => 'required|min:3|max:255',
            'description' => 'nullable',
            'is_active' => 'boolean'
        ];
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return CategoryResource::class;
    }
}

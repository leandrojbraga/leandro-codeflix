<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\GenreResource;
use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends BasicCrudController
{
    protected function model() {
        return Genre::class;
    }

    protected function validationRules($request) {
        return [
            'name' => 'required|min:3|max:255',
            'is_active' => 'boolean',
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
        ];
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return GenreResource::class;
    }
}

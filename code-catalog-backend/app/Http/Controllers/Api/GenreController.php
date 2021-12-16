<?php

namespace App\Http\Controllers\Api;

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
}

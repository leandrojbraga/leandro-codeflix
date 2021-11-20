<?php

namespace App\Http\Controllers\Api;

use App\Models\Genre;

class GenreController extends BasicCrudController
{
    protected function model() {
        return Genre::class;
    }

    protected function validationRules() {
        return [
            'name' => 'required|min:3|max:255',
            'is_active' => 'boolean'
        ];
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Rules\GenreHasCategoryRule;
use App\Rules\GenreRelationCategory;
use Illuminate\Http\Request;


class VideoController extends BasicCrudController
{
    protected function model() {
        return Video::class;
    }

    protected function validationRules($request) {
        return [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::RATINGS),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
            'genres_id' => [
                'required', 'array', 'exists:genres,id,deleted_at,NULL',
                new GenreHasCategoryRule($request->categories_id)
            ],
            'content_descriptors_id' => 'array|exists:content_descriptors,id,deleted_at,NULL'
        ];
    }
}

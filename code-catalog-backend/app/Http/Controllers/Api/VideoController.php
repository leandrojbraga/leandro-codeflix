<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use App\Rules\CategoryRelationGenre;
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
            'categories_id' => ['required', 'array', 'exists:categories,id', new CategoryRelationGenre($request->genres_id)],
            'genres_id' => ['required', 'array', 'exists:genres,id', new GenreRelationCategory($request->categories_id)],
            'content_descriptors_id' => 'required|array|exists:content_descriptors,id'
        ];
    }

    protected function handleRelations($transaction, Request $request)
    {
        $transaction->categories()->sync($request->get('categories_id'));
        $transaction->genres()->sync($request->get('genres_id'));
        $transaction->content_descriptors()->sync($request->get('content_descriptors_id'));
    }
}

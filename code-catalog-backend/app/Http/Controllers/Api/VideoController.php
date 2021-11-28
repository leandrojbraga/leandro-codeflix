<?php

namespace App\Http\Controllers\Api;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends BasicCrudController
{
    protected function model() {
        return Video::class;
    }

    protected function validationRules() {
        return [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::RATINGS),
            'duration' => 'required|integer'
        ];
    }
}

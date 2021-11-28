<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\VideoStub;

class VideoControllerStub extends BasicCrudController
{
    protected function model() {
        return VideoStub::class;
    }

    protected function validationRules() {
        return [
            'title' => 'required|max:255',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:L,A'
        ];
    }


}
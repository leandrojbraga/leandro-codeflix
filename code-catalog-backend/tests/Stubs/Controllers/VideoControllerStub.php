<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\VideoStub;
use Tests\Stubs\Resources\VideoResourceStub;

class VideoControllerStub extends BasicCrudController
{
    protected function model() {
        return VideoStub::class;
    }

    protected function validationRules($request) {
        return [
            'title' => 'required|max:255',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:L,A'
        ];
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return VideoResourceStub::class;
    }

}
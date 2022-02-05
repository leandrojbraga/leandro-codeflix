<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\CastMemberStub;
use Tests\Stubs\Resources\CastMemberResourceStub;

class CastMemberControllerStub extends BasicCrudController
{
    protected function model() {
        return CastMemberStub::class;
    }

    protected function validationRules($request) {
        return [
            'name' => 'required|max:255',
            'type' => 'required|in:1,2'
        ];
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return CastMemberResourceStub::class;
    }
}
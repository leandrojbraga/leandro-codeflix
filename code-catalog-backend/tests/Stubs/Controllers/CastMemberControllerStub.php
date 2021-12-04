<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\CastMemberStub;

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


}
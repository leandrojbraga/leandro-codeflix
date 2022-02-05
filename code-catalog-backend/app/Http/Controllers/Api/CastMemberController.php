<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;
use Illuminate\Validation\Rule;

class CastMemberController extends BasicCrudController
{
    protected function model() {
        return CastMember::class;
    }

    protected function validationRules($request) {
        return [
            'name' => 'required|min:3|max:255',
            'type' => [
                'required',
                Rule::in(array_keys(CastMember::TYPES)),
            ]
        ];
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return CastMemberResource::class;
    }
}

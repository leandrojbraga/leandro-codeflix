<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ContentDescriptorResource;
use App\Models\ContentDescriptor;

class ContentDescriptorController extends BasicCrudController
{
    protected function model() {
        return ContentDescriptor::class;
    }

    protected function validationRules($request) {
        return [
            'name' => 'required|min:3|max:50',
        ];
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }

    protected function resource()
    {
        return ContentDescriptorResource::class;
    }
}

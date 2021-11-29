<?php

namespace App\Http\Controllers\Api;

use App\Models\ContentDescriptor;

class ContentDescriptorController extends BasicCrudController
{
    protected function model() {
        return ContentDescriptor::class;
    }

    protected function validationRules() {
        return [
            'name' => 'required|min:3|max:50',
        ];
    }
}

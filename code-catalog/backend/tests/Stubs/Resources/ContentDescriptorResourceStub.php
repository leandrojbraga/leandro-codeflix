<?php

namespace Tests\Stubs\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContentDescriptorResourceStub extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
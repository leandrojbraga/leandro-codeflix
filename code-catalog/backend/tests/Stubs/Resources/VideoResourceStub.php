<?php

namespace Tests\Stubs\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResourceStub extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'year_launched' => $this->year_launched,
            'opened' => $this->opened,
            'rating' => $this->rating,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
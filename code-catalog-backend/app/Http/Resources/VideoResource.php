<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $video = parent::toArray($request);
        
        $video['categories'] = CategoryResource::collection($this->categories);

        $video['genres'] = GenreResource::collection($this->genres);

        $video['content_descriptors'] = ContentDescriptorResource::collection($this->content_descriptors);

        return $video;
    }
}

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
        return parent::toArray($request) + [
            'categories' => CategoryResource::collection($this->categories),
            'genres' => GenreResource::collection($this->genres),
            'content_descriptors' => ContentDescriptorResource::collection($this->content_descriptors),
            'trailer_file_url' => $this->trailer_file_url,
            'movie_file_url' => $this->movie_file_url,
            'thumbnail_file_url' => $this->thumbnail_file_url,
            'banner_file_url' => $this->banner_file_url
        ];
    }
}

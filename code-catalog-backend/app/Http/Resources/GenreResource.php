<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GenreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   
        $genre = parent::toArray($request);
        
        $genre['categories'] = CategoryResource::collection($this->categories);
        
        return $genre;

    }
}

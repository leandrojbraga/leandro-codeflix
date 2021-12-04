<?php

namespace App\Rules;

use App\Models\Genre;
use Illuminate\Contracts\Validation\Rule;

class CategoryRelationGenre implements Rule
{   
    public $attribute;
    public $genres;
    public $categoryNotRelated;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($genre_id)
    {
        $this->genres = $genre_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {   
        $this->attribute = str_replace('_', ' ', $attribute);

        if (is_array($value) && is_array($this->genres)) {
            foreach($value as $id) {
                $count = Genre::whereIn('id', $this->genres)
                ->whereHas('categories', function ($query) use($id) {
                    $query->where('id', $id);
                })->get()->count();
                
                if ($count == 0){
                    $this->categoryNotRelated = $id;
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.related_attribute', [
            'attribute' => $this->attribute,
            'value' => $this->categoryNotRelated,
            'relationship' => 'genre'
        ]);
    }
}

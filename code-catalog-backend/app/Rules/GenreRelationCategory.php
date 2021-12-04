<?php

namespace App\Rules;

use App\Models\Genre;
use Illuminate\Contracts\Validation\Rule;

class GenreRelationCategory implements Rule
{   
    public $attribute;
    public $categories;
    public $genreNotRelated;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($category_id)
    {
        $this->categories = $category_id;
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

        if (is_array($value) && is_array($this->categories)) {
            foreach($value as $id) {
                $genre = Genre::find($id);

                if ($genre){
                    $count = $genre->categories()
                        ->whereIn('id', $this->categories)
                        ->count();
                
                    if ($count == 0){
                        $this->genreNotRelated = $id;
                        return false;
                    }
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
            'value' => $this->genreNotRelated,
            'relationship' => 'category'
        ]);
    }
}

<?php
declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GenreHasCategoryRule implements Rule
{   
    private $attribute;
    private $categoriesId;
    private $genresId;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($categoriesId)
    {
        $this->categoriesId = is_array($categoriesId) 
                ? array_unique($categoriesId)
                : [];
    }

    protected function getRelationCategories($genreId): Collection
    {
        return DB::table('category_genre')
            ->where('genre_id', $genreId)
            ->whereIn('category_id', $this->categoriesId)
            ->get();
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
        $this->genresId = is_array($value) 
            ? array_unique($value)
            : [];

        if (!count($this->genresId) || !count($this->categoriesId)) {
            return false;
        }

        $relationCategoriesFound = [];
        foreach($this->genresId as $genreId) {
            $relations = $this->getRelationCategories($genreId);
            if (!$relations->count()) {
                return false;
            }

            array_push(
                $relationCategoriesFound,
                ...$relations->pluck('category_id')->toArray()
            );
        }

        $relationCategoriesFound = array_unique($relationCategoriesFound);

        if (count($this->categoriesId) !== count($relationCategoriesFound)) {
            return false;
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
            'relationship' => 'category id'
        ]);
    }
}

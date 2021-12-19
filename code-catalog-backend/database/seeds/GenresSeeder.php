<?php

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $categories = Category::all();
        factory(Genre::class, 100)->create()
            ->each(function(Genre $genre) use ($categories) {
                $categoriesId = $categories->random(rand(1,3))
                    ->pluck('id')->toArray();
                $genre->categories()->attach($categoriesId);
            });
    }
}

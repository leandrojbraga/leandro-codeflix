<?php

use App\Models\ContentDescriptor;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Seeder;

class VideosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $genres = Genre::all();
        $ContentDescriptors = ContentDescriptor::all();

        factory(Video::class, 100)->create()
            ->each(function(Video $video) use ($genres, $ContentDescriptors) {
                $randGenres = $genres->random(rand(1,4));
                $genresId = $randGenres->pluck('id')->toArray();
                $video->genres()->attach($genresId);

                $categoriesId = [];
                foreach($randGenres as $genre) {
                    array_push(
                        $categoriesId,
                        ...$genre->categories->pluck('id')->toArray()
                    );
                }
                $categoriesId = array_unique($categoriesId);
                $video->categories()->attach($categoriesId);
                
                $contentDescriptorsId = $ContentDescriptors->random(rand(0,3))
                    ->pluck('id')->toArray();
                $video->content_descriptors()->attach($contentDescriptorsId);
            });
    }
}

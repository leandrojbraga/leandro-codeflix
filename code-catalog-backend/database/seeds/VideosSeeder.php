<?php

use App\Models\ContentDescriptor;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VideosSeeder extends Seeder
{   
    private $allGenres;
    private $allContentDescriptors;
    private $relations;
    private $files;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $dir = Storage::getDriver()->getAdapter()->getPathPrefix();
        File::deleteDirectory($dir, true);

        $self = $this;
        $this->allGenres = Genre::all();
        $this->allContentDescriptors = ContentDescriptor::all();
        Model::reguard();

        factory(Video::class, 50)
            ->make()
            ->each(function(Video $video) use ($self) {
                $self->fetchFiles();
                $self->fetchRelations();
                
                Video::create(
                    array_merge(
                        $video->toArray(),
                        $this->files,
                        $this->relations
                    )
                );
            });
        
        Model::unguard();
    }

    private function fetchRelations() {
        $randGenres = $this->allGenres->random(rand(1,4));
        $genresId = $randGenres->pluck('id')->toArray();

        $categoriesId = [];
        foreach($randGenres as $genre) {
            array_push(
                $categoriesId,
                ...$genre->categories->pluck('id')->toArray()
            );
        }
        $categoriesId = array_unique($categoriesId);

        $contentDescriptorsId = $this->allContentDescriptors->random(rand(0,3))
            ->pluck('id')->toArray();

        $this->relations = [
            'genres_id' => $genresId,
            'categories_id' => $categoriesId,
            'content_descriptors_id' => $contentDescriptorsId
        ];
    }

    private function getImageFile() {
        return new UploadedFile(
            storage_path('faker/media/images/codeflix_laravel.png'),
            'codeflix_laravel.png'
        );
    }

    private function getVideoFile() {
        return new UploadedFile(
            storage_path('faker/media/videos/codeflix_uploads.mp4'),
            'codeflix_uploads.mp4'
        );
    }

    private function fetchFiles() {
        $this->files = [
            'trailer_file' => $this->getVideoFile(),
            'movie_file' => $this->getVideoFile(),
            'thumbnail_file' => $this->getImageFile(),
            'banner_file' => $this->getImageFile()
        ];
    }

}

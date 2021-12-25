<?php

namespace App\Models;

use App\Models\Traits\UploadFiles;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Video extends Model
{
    use SoftDeletes, Uuid, UploadFiles;

    const RATINGS = [
        'L', '10', '12', '14', '16', '18'
    ];

    const MAX_SIZE_MOVIE_FILE = 4096;
    const MIME_TYPE_MOVIE_FILE = 'video/mp4';
    const MAX_SIZE_THUMBNAIL_FILE = 4096;
    const MIME_TYPE_THUMBNAIL_FILE = ['image/jpeg', 'image/png'];

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'movie_file',
        'thumbnail_file'
    ];
    public static $fileFields = [
        'movie_file', 'thumbnail_file'
    ];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string',
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    public static function handleRelations(Video $video, array $attributes)
    {   
        if (isset($attributes['categories_id'])) {
            $video->categories()->sync($attributes['categories_id']);
        }
        if (isset($attributes['genres_id'])) {
            $video->genres()->sync($attributes['genres_id']);
        }
        if (isset($attributes['content_descriptors_id'])) {
            $video->content_descriptors()->sync($attributes['content_descriptors_id']);
        }
    }

    public static function create(array $attributes = []) {
        $files = self::extractFiles($attributes);

        try {
            DB::beginTransaction();
            /** @var Video $video */
            $video = static::query()->create($attributes);
            static::handleRelations($video, $attributes);

            $video->uploadFiles($files);
            
            DB::commit();
            
        } catch (\Exception $err) {
            if (isset($video)) {
                $video->deleteFiles($files);
            }
            DB::rollBack();
            
            throw $err;
        }
        
        return $video;
    }

    public function update(array $attributes = [], array $options = []) {
        $files = self::extractFiles($attributes);

        try {
            DB::beginTransaction();
            $updated = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if ($updated) {
                $this->uploadFiles($files);
            }
            DB::commit();
            if ($updated && count($files)) {
                $this->deleteOldFiles();
            }
        } catch (\Exception $err) {
            $this->deleteFiles($files);
            
            DB::rollBack();
            throw $err;
        }
        
        return $updated;
    }

    protected $with = [
        'categories', 'genres', 'content_descriptors'
    ];

    public function categories() {
        return $this->belongsToMany(Category::class)
            ->select(array('categories.id','categories.name'))
            ->withTrashed();
    }

    public function genres() {
        return $this->belongsToMany(Genre::class)
            ->select(array('genres.id','genres.name'))
            ->withTrashed();
    }

    public function content_descriptors() {
        return $this->belongsToMany(ContentDescriptor::class)
            ->select(array('content_descriptors.id','content_descriptors.name'))
            ->withTrashed();
    }
    
    protected function uploadDir()
    {
        return $this->id;
    }

}

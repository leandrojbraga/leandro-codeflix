<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Video extends Model
{
    use SoftDeletes, Uuid;

    const RATINGS = [
        'L', '10', '12', '14', '16', '18'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration'
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
        try {
            DB::beginTransaction();
            $video = static::query()->create($attributes);
            static::handleRelations($video, $attributes);
            // upload
            DB::commit();
        } catch (\Exception $err) {
            if (isset($video)) {
                // delete upload
            }
            DB::rollBack();
            throw $err;
        }
        
        return $video;
    }

    public function update(array $attributes = [], array $options = []) {
        try {
            DB::beginTransaction();
            $updated = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);
            if ($updated) {
                // upload new
                // delete old
            }
            DB::commit();
        } catch (\Exception $err) {
            if (isset($updated)) {
                // delete upload new
            }
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
    

}

<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Genre extends Model
{
    use SoftDeletes, Uuid;

    public $incrementing = false; 
    protected $keyType = 'string';
    protected $fillable = ['name', 'is_active'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean'
    ];

    public static function handleRelations(Genre $genre, array $attributes)
    {   
        if (isset($attributes['categories_id'])) {
            $genre->categories()->sync($attributes['categories_id']);
        }
    }

    public static function create(array $attributes = []) {
        try {
            DB::beginTransaction();
            /** @var Genre $video */
            $genre = static::query()->create($attributes);
            static::handleRelations($genre, $attributes);
            
            DB::commit();
        } catch (\Exception $err) {
            DB::rollBack();
            throw $err;
        }
        
        return $genre;
    }

    public function update(array $attributes = [], array $options = []) {
        try {
            DB::beginTransaction();

            $updated = parent::update($attributes, $options);
            static::handleRelations($this, $attributes);

            DB::commit();
        } catch (\Exception $err) {
            DB::rollBack();
            throw $err;
        }
        
        return $updated;
    }

    protected $with = [
        'categories'
    ];

    public function categories() {
        return $this->belongsToMany(Category::class)->withTrashed();
    }
}

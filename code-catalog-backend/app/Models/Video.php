<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'opened' => 'boolean',
        'year_launched' => 'integer',
        'duration' => 'integer'
    ];

    protected $with = [
        'categories', 'genres', 'content_descriptors'
    ];

    public function categories() {
        return $this->belongsToMany(Category::class)
            ->select(array('categories.id','categories.name'));
    }

    public function genres() {
        return $this->belongsToMany(Genre::class)
            ->select(array('genres.id','genres.name'));
    }

    public function content_descriptors() {
        return $this->belongsToMany(ContentDescriptor::class)
            ->select(array('content_descriptors.id','content_descriptors.name'));
    }
    

}

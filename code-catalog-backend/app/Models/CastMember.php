<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CastMember extends Model
{
    use SoftDeletes, Uuid;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;
    
    const TYPES = [
        self::TYPE_DIRECTOR => 'Diretor',
        self::TYPE_ACTOR => 'Actor'
    ];

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'type'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'type' => 'string'
    ];

    public function getTypeAttribute()
    {
        return self::TYPES[$this->attributes['type']];
    }
    public function setTypeAttribute($type)
    {
        $this->attributes['type'] = $type;
    }
}

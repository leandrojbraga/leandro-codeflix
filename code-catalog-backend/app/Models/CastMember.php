<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Enums\CastMemberType;


class CastMember extends Model
{
    use SoftDeletes, Uuid;

    public const TYPES = [
        1 => 'Diretor',
        2 => 'Actor'
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

    
    // public static function getTypeAttribute($type)
    // {
    //    return array_search($type, self::TYPES);
    // }

    // public function getTypeAttribute()
    // {
    //    return self::TYPES[ $this->attributes['type'] ];
    // }


}

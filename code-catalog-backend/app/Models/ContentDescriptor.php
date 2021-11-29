<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentDescriptor extends Model
{
    use SoftDeletes, Uuid;

    public $incrementing = false; 
    protected $keyType = 'string';
    protected $fillable = ['name'];
    protected $dates = ['deleted_at'];
}

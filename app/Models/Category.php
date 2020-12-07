<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Category extends Model
{
    use SoftDeletes, Traits\Uuid;

    protected $fillable = ['name', 'is_active', 'description'];
    protected $dates = ['deleted_at',];
    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
    ];
    public $incrementing = false;
}

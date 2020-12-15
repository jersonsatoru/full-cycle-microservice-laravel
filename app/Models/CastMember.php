<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes, Uuid;

    protected $fillable = ['name', 'type'];
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $casts = [
        'id' => 'string',
    ];
}

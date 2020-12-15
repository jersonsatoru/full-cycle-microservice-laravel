<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use App\Http\Controllers\Api\BasicCrudController;

class CastMemberController extends BasicCrudController
{
    protected function model()
    {
        return CastMember::class;
    }

    protected function rulesStore()
    {
        return [
            'name' => 'required|max:255',
            'type' => 'max:2|min:1',
        ];
    }

    protected function rulesUpdate()
    {
        return [
            'name' => 'required|max:255',
            'type' => 'max:2|min:1',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model {
    protected $fillable = [
        'name',
        'image',
        'job_title',
        'linkin_url',
        'status',
    ];
}

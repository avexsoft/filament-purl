<?php

namespace Avexsoft\FilamentPurl\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'slug',
        'url',
        'description',
        'is_active',
    ];
}

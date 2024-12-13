<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Color extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'code'];

    public $translatable = ['name'];
}

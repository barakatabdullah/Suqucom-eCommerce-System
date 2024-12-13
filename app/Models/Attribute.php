<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use HasTranslations;

    public $translatable = ['name'];


    public function attributeValues() : HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }
}

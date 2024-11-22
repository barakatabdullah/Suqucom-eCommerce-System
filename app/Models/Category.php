<?php

namespace App\Models;

use App\Models\Scopes\CategoryActiveScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([CategoryActiveScope::class])]
class Category extends Model
{
    use HasFactory;
    use HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name', 'slug', 'image', 'active', 'order', 'published', 'icon', 'color', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class,'product_categories', 'category_id', 'product_id');
    }

}

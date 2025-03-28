<?php

namespace App\Models;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([ActiveScope::class])]
class Category extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
    protected $fillable = ['name', 'slug', 'image', 'active', 'order', 'published', 'icon', 'color', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('categories');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class,'product_categories', 'category_id', 'product_id');
    }

}

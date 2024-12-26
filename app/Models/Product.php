<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;


class Product extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;
    protected $fillable=[
        'name',
        'description',
        'purchase_price',
        'published',
        'slug',
        'approved',
        'brand_id',
        'cash_on_delivery',
        'free_shipping',
        'user_id',
        'stock',
        'discount_type',
        'discount',
        'discount_start',
        'discount_end',
        'external_link',
        'num_of_sales',
        'rating'


    ];

    public $translatable = ['name', 'description'];


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }


    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }


    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }



}

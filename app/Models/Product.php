<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'meta_keywords' => 'array'
    ];

    protected $appends = ['image','averageRating','all_image_url'];

    protected $dates = ['deleted_at'];



    public function getImageAttribute()
    {
        return getFile($this->driver,$this->thumbnail_image);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function details()
    {
        return $this->hasOne(ProductDetails::class, 'product_id');
    }

    public function getLanguageEditClass($id, $languageId)
    {
        return DB::table('product_details')->where(['product_id' => $id, 'language_id' => $languageId])->exists() ? 'bi-check2' : 'bi-pencil';
    }

    public function wishlist() : HasMany
    {
        return $this->hasMany(WishList::class, 'product_id', 'id');
    }

    public function reviews() : HasMany
    {
        return $this->hasMany(Rating::class, 'product_id', 'id');
    }

    public function getAverageRatingAttribute()
    {
        $reviews = $this->reviews;
        $totalReviews = $reviews->count();
        $totalRatings = $reviews->sum('rating');
        $averageRating = $totalReviews > 0 ? $totalRatings / $totalReviews : 0;
        return $averageRating;
    }

    public function images() : HasMany
    {
        return  $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function ProductMaterial() :BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id', 'id');
    }

    public function ProductKarat() :BelongsTo
    {
        return $this->belongsTo(Karat::class, 'karat_id', 'id');
    }

    public function getMetaRobots()
    {
        return explode(",", $this->meta_robots);
    }

    public function getAllImageUrlAttribute()
    {
        $images = [];
        foreach ($this->images??[] as $image) {
            $images[] = getFile($image->driver,$image->image);
        }
        return $images;
    }
}

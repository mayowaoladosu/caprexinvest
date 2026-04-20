<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $guarded = ['id'];

    public function subcategories() :HasMany
    {
        return $this->hasMany(SubCategory::class,'category_id');
    }

    public function activeSubcategories()
    {
        return $this->hasMany(SubCategory::class,'category_id')->where('status',1);
    }

    public function products() :HasMany
    {
        return $this->hasMany(Product::class,'category_id')->where('status',1);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $guarded = ['id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'productcategory_id',
        'productstate_id',
        'brand',
        'name',
        'detail',
        'value',
        'image',
        'soldflg',
        'favoriteflg',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'soldflg' => 'boolean',
        'favoriteflg' => 'boolean',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'productcategory_id');
    }

    /**
     * Get the state that owns the product.
     */
    public function state()
    {
        return $this->belongsTo(ProductState::class, 'productstate_id');
    }

    /**
     * Get the users that have this product.
     */
    public function userProductRelations()
    {
        return $this->hasMany(UserProductRelation::class);
    }

    /**
     * Get the comments for the product through user product relations.
     */
    public function comments()
    {
        return $this->hasManyThrough(Comment::class, UserProductRelation::class, 'product_id', 'userproductrelation_id');
    }
}

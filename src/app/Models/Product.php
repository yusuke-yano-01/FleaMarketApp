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
        'productbrand_id',
        'name',
        'detail',
        'value',
        'image',
        'soldflg',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'decimal:2',
        'soldflg' => 'boolean',
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
     * Get the brand that owns the product.
     */
    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'productbrand_id');
    }

    /**
     * Get the users that have this product.
     */
    public function userProductRelations()
    {
        return $this->hasMany(UserProductRelation::class);
    }
}

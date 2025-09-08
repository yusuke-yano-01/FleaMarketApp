<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProductRelation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'user_id',
        'userproducttype_id',
    ];

    /**
     * Get the user that owns the relation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that owns the relation.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the type that owns the relation.
     */
    public function type()
    {
        return $this->belongsTo(UserProductType::class, 'userproducttype_id');
    }

    /**
     * Get the comments for the user product relation.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

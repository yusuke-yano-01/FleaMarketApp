<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductState extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'state',
        'name',
    ];

    /**
     * Get the products for the state.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'productstate_id');
    }
}

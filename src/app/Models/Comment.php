<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'userproductrelation_id',
        'comment',
    ];

    /**
     * Get the user product relation that owns the comment.
     */
    public function userProductRelation()
    {
        return $this->belongsTo(UserProductRelation::class);
    }
}

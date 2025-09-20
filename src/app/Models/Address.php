<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'address';

    protected $fillable = [
        'postcode',
        'address',
        'building',
    ];

    protected $casts = [
        'postcode' => 'string',
        'address' => 'string',
        'building' => 'string',
    ];
}
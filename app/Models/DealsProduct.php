<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealsProduct extends Model
{
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'deals_id',
        'product_id',
        'quantity',
        'unit'
    ];
}
